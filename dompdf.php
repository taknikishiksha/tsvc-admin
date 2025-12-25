<?php

return [

    'show_warnings' => false,

    'public_path' => null,

    'convert_entities' => true,

    'options' => [

        // Font directory & cache
        'font_dir'   => storage_path('fonts'),
        'font_cache' => storage_path('fonts'),

        // Temporary directory
        'temp_dir' => sys_get_temp_dir(),

        // Base path restriction
        'chroot' => realpath(base_path()),

        // Allowed protocols
        'allowed_protocols' => [
            'data://'   => ['rules' => []],
            'file://'   => ['rules' => []],
            'http://'   => ['rules' => []],
            'https://'  => ['rules' => []],
        ],

        'artifactPathValidation' => null,
        'log_output_file' => null,

        // ENABLE FONT SUBSETTING (for smaller PDF & exact glyphs)
        'enable_font_subsetting' => true,

        // Backend rendering engine
        'pdf_backend' => 'CPDF',

        // Media type
        'default_media_type' => 'screen',

        // PAPER & ORIENTATION (BEST FOR CERTIFICATE)
        'default_paper_size'         => 'a4',
        'default_paper_orientation'  => 'landscape',

        // DEFAULT FONT FOR CERTIFICATES
        'default_font' => 'Helvetica',

        // IMAGE QUALITY
        'dpi' => 150,

        // EMBEDDED PHP IN HTML (KEEP OFF FOR SECURITY)
        'enable_php' => false,

        // PDF JavaScript support
        'enable_javascript' => true,

        // *** IMPORTANT FOR LOGO & BACKGROUNDS ***
        'enable_remote' => true,
        'allowed_remote_hosts' => null,

        // Text metrics
        'font_height_ratio' => 1.1,

        // HTML5 parser always ON
        'enable_html5_parser' => true,
    ],
];
