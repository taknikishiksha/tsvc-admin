<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FileValidationService
{
    private $allowedMimeTypes = [
        'image/jpeg',
        'image/jpg', 
        'image/png',
        'image/gif',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    private $allowedExtensions = [
        'jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'
    ];

    private $maxFileSize = 10 * 1024 * 1024; // 10MB

    /**
     * Validate file security and integrity
     */
    public function validateFile($fileContent, $filename, $mimeType)
    {
        try {
            // Decode base64 content
            $decodedContent = base64_decode($fileContent);
            if ($decodedContent === false) {
                throw new \Exception('Invalid file content encoding');
            }

            $fileSize = strlen($decodedContent);

            // Check file size
            if ($fileSize > $this->maxFileSize) {
                throw new \Exception('File size exceeds maximum allowed limit (10MB)');
            }

            if ($fileSize === 0) {
                throw new \Exception('File is empty');
            }

            // Validate MIME type
            if (!$this->isAllowedMimeType($mimeType)) {
                throw new \Exception('File type not allowed: ' . $mimeType);
            }

            // Validate file extension
            if (!$this->isAllowedExtension($filename)) {
                throw new \Exception('File extension not allowed: ' . pathinfo($filename, PATHINFO_EXTENSION));
            }

            // Check for malicious content patterns
            $securityCheck = $this->performSecurityChecks($decodedContent, $filename, $mimeType);
            if (!$securityCheck['safe']) {
                throw new \Exception('File failed security check: ' . $securityCheck['reason']);
            }

            // Calculate file hash for duplicate detection
            $fileHash = md5($decodedContent);

            return [
                'is_safe' => true,
                'file_size' => $fileSize,
                'file_hash' => $fileHash,
                'scan_result' => 'clean',
                'message' => 'File validation successful'
            ];

        } catch (\Exception $e) {
            Log::warning('File validation failed', [
                'filename' => $filename,
                'mime_type' => $mimeType,
                'error' => $e->getMessage()
            ]);

            return [
                'is_safe' => false,
                'file_size' => 0,
                'file_hash' => null,
                'scan_result' => 'failed',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Check if MIME type is allowed
     */
    private function isAllowedMimeType($mimeType)
    {
        return in_array(strtolower($mimeType), $this->allowedMimeTypes);
    }

    /**
     * Check if file extension is allowed
     */
    private function isAllowedExtension($filename)
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, $this->allowedExtensions);
    }

    /**
     * Perform security checks on file content
     */
    private function performSecurityChecks($content, $filename, $mimeType)
    {
        // Check for PHP tags in non-PHP files
        if ($this->containsPhpTags($content) && !$this->isPhpFile($mimeType, $filename)) {
            return ['safe' => false, 'reason' => 'Potential PHP code in non-PHP file'];
        }

        // Check for JavaScript in non-JS files
        if ($this->containsJavaScript($content) && !$this->isJavaScriptFile($mimeType, $filename)) {
            return ['safe' => false, 'reason' => 'Potential JavaScript code in non-JS file'];
        }

        // Check for suspicious patterns
        if ($this->containsSuspiciousPatterns($content)) {
        return ['safe' => false, 'reason' => 'Suspicious patterns detected in file'];
        }

        // Check for executable patterns
        if ($this->containsExecutablePatterns($content)) {
            return ['safe' => false, 'reason' => 'Executable patterns detected'];
        }

        // Validate image integrity (for images)
        if (str_starts_with($mimeType, 'image/')) {
            $imageCheck = $this->validateImageIntegrity($content, $mimeType);
            if (!$imageCheck['safe']) {
                return $imageCheck;
            }
        }

        // Validate PDF integrity
        if ($mimeType === 'application/pdf') {
            $pdfCheck = $this->validatePdfIntegrity($content);
            if (!$pdfCheck['safe']) {
                return $pdfCheck;
            }
        }

        return ['safe' => true, 'reason' => 'All security checks passed'];
    }

    /**
     * Check for PHP tags
     */
    private function containsPhpTags($content)
    {
        $phpPatterns = [
            '/<\?php/i',
            '/<\?=/i',
            '/<\?\\s/i',
            '/<script\\s+language\\s*=\\s*["\']?php["\']?/i'
        ];

        foreach ($phpPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check for JavaScript - ENHANCED DETECTION
     */
    private function containsJavaScript($content)
    {
        $jsPatterns = [
            '/<script\\s*>.*?<\\/script>/is',
            '/javascript:/i',
            '/onload\\s*=/i',
            '/onerror\\s*=/i',
            '/onclick\\s*=/i',
            '/eval\\s*\\(/i',
            '/alert\\s*\\(/i',
            '/confirm\\s*\\(/i',
            '/prompt\\s*\\(/i'
        ];

        foreach ($jsPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check for suspicious patterns
     */
    private function containsSuspiciousPatterns($content)
    {
        $suspiciousPatterns = [
            '/base64_decode/i',
            '/gzinflate/i',
            '/system\\s*\\(/i',
            '/exec\\s*\\(/i',
            '/shell_exec/i',
            '/passthru/i'
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check for executable patterns
     */
    private function containsExecutablePatterns($content)
    {
        // Check for ELF header (Linux executable)
        if (str_starts_with($content, "\x7FELF")) {
            return true;
        }

        // Check for MZ header (Windows executable)
        if (str_starts_with($content, "MZ")) {
            return true;
        }

        // Check for shebang (script executable)
        if (str_starts_with($content, "#!/")) {
            return true;
        }

        return false;
    }

    /**
     * Validate image integrity
     */
    private function validateImageIntegrity($content, $mimeType)
    {
        try {
            // Basic image header validation
            $imageHeaders = [
                'image/jpeg' => "\xFF\xD8\xFF",
                'image/png' => "\x89PNG\r\n\x1A\n",
                'image/gif' => "GIF"
            ];

            if (isset($imageHeaders[$mimeType])) {
                $header = $imageHeaders[$mimeType];
                if (!str_starts_with($content, $header)) {
                    return ['safe' => false, 'reason' => 'Invalid image header'];
                }
            }

            // Check for image size limits
            $imageInfo = @getimagesizefromstring($content);
            if ($imageInfo === false) {
                return ['safe' => false, 'reason' => 'Invalid image data'];
            }

            // Prevent extremely large dimensions (potential decompression bomb)
            $maxDimension = 5000;
            if ($imageInfo[0] > $maxDimension || $imageInfo[1] > $maxDimension) {
                return ['safe' => false, 'reason' => 'Image dimensions too large'];
            }

            return ['safe' => true, 'reason' => 'Image validation passed'];

        } catch (\Exception $e) {
            return ['safe' => false, 'reason' => 'Image validation failed: ' . $e->getMessage()];
        }
    }

    /**
     * Validate PDF integrity - FINAL FIX FOR JAVASCRIPT DETECTION
     */
    private function validatePdfIntegrity($content)
    {
        // Check PDF header
        if (!str_starts_with($content, "%PDF-")) {
            return ['safe' => false, 'reason' => 'Invalid PDF header'];
        }

        // Check PDF trailer
        if (strpos($content, "%%EOF") === false) {
            return ['safe' => false, 'reason' => 'Invalid PDF structure'];
        }

        // ENHANCED JavaScript detection for PDF files
        $javascriptPatterns = [
            '/\/JavaScript/i',
            '/\/JS/i',
            '/javascript:/i',
            '/<script/i',
            '/eval\s*\(/i',
            '/alert\s*\(/i',
            '/confirm\s*\(/i',
            '/prompt\s*\(/i',
            '/onload/i',
            '/onerror/i',
            '/onclick/i'
        ];

        foreach ($javascriptPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return ['safe' => false, 'reason' => 'PDF contains JavaScript - potential security risk'];
            }
        }

        return ['safe' => true, 'reason' => 'PDF validation passed'];
    }

    /**
     * Check if file is PHP file
     */
    private function isPhpFile($mimeType, $filename)
    {
        return $mimeType === 'application/x-php' || 
               pathinfo($filename, PATHINFO_EXTENSION) === 'php';
    }

    /**
     * Check if file is JavaScript file
     */
    private function isJavaScriptFile($mimeType, $filename)
    {
        return $mimeType === 'application/javascript' ||
               $mimeType === 'text/javascript' ||
               pathinfo($filename, PATHINFO_EXTENSION) === 'js';
    }

    /**
     * Get allowed MIME types
     */
    public function getAllowedMimeTypes()
    {
        return $this->allowedMimeTypes;
    }

    /**
     * Get allowed extensions
     */
    public function getAllowedExtensions()
    {
        return $this->allowedExtensions;
    }

    /**
     * Get maximum file size
     */
    public function getMaxFileSize()
    {
        return $this->maxFileSize;
    }

    /**
     * Format maximum file size for display
     */
    public function getFormattedMaxFileSize()
    {
        $size = $this->maxFileSize;
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        
        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }
        
        return round($size, 2) . ' ' . $units[$unitIndex];
    }
}