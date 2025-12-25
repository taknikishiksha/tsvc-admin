<?php
// Derive meta from child views (safe fallback chain)
$metaTitle       = trim($__env->yieldContent('meta_title'))
    ?: (trim($__env->yieldContent('title')) ?: config('app.name', 'Takniki Shiksha Careers'));

$metaDescription = trim($__env->yieldContent('meta_description'))
    ?: 'Takniki Shiksha Careers — Yoga courses, teachers, services, jobs, internships, and corporate wellness programs.';

$metaKeywords    = trim($__env->yieldContent('meta_keywords'))
    ?: 'yoga, YCB, yoga teacher, yoga classes, corporate wellness, internship, jobs, Takniki Shiksha';

$metaImage       = trim($__env->yieldContent('meta_image')) ?: asset('photos/tsvc.png');
$canonicalUrl    = trim($__env->yieldContent('canonical')) ?: url()->current();
$siteName        = config('app.name', 'Takniki Shiksha Careers');

// JSON-LD payloads (encoded safely below)
$websiteSchema = [
    '@context' => 'https://schema.org',
    '@type'    => 'WebSite',
    'name'     => $siteName,
    'url'      => url('/'),
    'potentialAction' => [
        '@type'       => 'SearchAction',
        'target'      => url('/').'?q={search_term_string}',
        'query-input' => 'required name=search_term_string',
    ],
];
?>

<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<meta name="keywords" content="{{ $metaKeywords }}">
<link rel="canonical" href="{{ $canonicalUrl }}"/>

<meta name="robots" content="index,follow">
<meta name="author" content="Takniki Shiksha">
<meta name="theme-color" content="#0698ac">

{{-- Open Graph --}}
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:site_name" content="{{ $siteName }}">

{{-- Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $metaImage }}">

{{-- JSON-LD (Website) --}}
<script type="application/ld+json">
{!! json_encode($websiteSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
</script>
