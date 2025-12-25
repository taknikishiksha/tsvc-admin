<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Takniki Shiksha Careers' }}</title>

    <!-- Meta Tags -->
    {{ $meta ?? '' }}

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="flex flex-col min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow sticky top-0 z-50">
            <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-between items-center">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('photos/tsvc.png') }}" alt="Takniki Shiksha Logo" class="h-10 w-auto">
                        </a>
                    </div>
                    <!-- Desktop Navigation Links -->
                    <div class="hidden md:flex space-x-8">
                        <a href="{{ url('/') }}" class="text-gray-900 hover:text-[#0698ac] font-medium">Home</a>
                        <a href="{{ url('services') }}" class="text-gray-900 hover:text-[#0698ac] font-medium">Services</a>
                        <a href="{{ url('courses') }}" class="text-gray-900 hover:text-[#0698ac] font-medium">Courses</a>
                        <a href="{{ url('careers') }}" class="text-gray-900 hover:text-[#0698ac] font-medium">Jobs</a>
                        <a href="{{ url('blogs') }}" class="text-gray-900 hover:text-[#0698ac] font-medium">Blog</a>
                        <a href="{{ url('contact') }}" class="text-gray-900 hover:text-[#0698ac] font-medium">Contact</a>
                    </div>
                    <!-- Desktop Search Bar -->
                    @include('components.search-bar')
                    <!-- Mobile Menu Button -->
                    <div class="md:hidden">
                        <button type="button" class="text-gray-900 hover:text-[#0698ac] focus:outline-none" id="mobile-menu-button">
                            <i class="fas fa-bars text-2xl"></i>
                        </button>
                    </div>
                </div>
                <!-- Mobile Menu -->
                <div class="mobile-menu hidden md:hidden" id="mobile-menu">
                    <div class="pt-2 pb-3 space-y-1">
                        <a href="{{ url('/') }}" class="block px-3 py-2 text-gray-900 hover:bg-[#0698ac] hover:text-white">Home</a>
                        <a href="{{ url('services') }}" class="block px-3 py-2 text-gray-900 hover:bg-[#0698ac] hover:text-white">Services</a>
                        <a href="{{ url('courses') }}" class="block px-3 py-2 text-gray-900 hover:bg-[#0698ac] hover:text-white">Courses</a>
                        <a href="{{ url('careers') }}" class="block px-3 py-2 text-gray-900 hover:bg-[#0698ac] hover:text-white">Jobs</a>
                        <a href="{{ url('blogs') }}" class="block px-3 py-2 text-gray-900 hover:bg-[#0698ac] hover:text-white">Blog</a>
                        <a href="{{ url('contact') }}" class="block px-3 py-2 text-gray-900 hover:bg-[#0698ac] hover:text-white">Contact</a>
                    </div>
                </div>
            </nav>
        </header>

        <!-- Main Content Area with Sidebar -->
        <div class="flex flex-1">
            <!-- Sidebar -->
            <aside class="hidden lg:block w-64 bg-white shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Links</h3>
                <ul class="space-y-3">
                    <li><a href="{{ url('apply') }}" class="text-[#0698ac] hover:text-[#1d5055] font-medium"><i class="fas fa-user-plus mr-2"></i>Register Now</a></li>
                    <li><a href="{{ url('training') }}" class="text-[#0698ac] hover:text-[#1d5055] font-medium"><i class="fas fa-chalkboard-teacher mr-2"></i>Become a Teacher</a></li>
                    <li><a href="{{ url('careers') }}" class="text-[#0698ac] hover:text-[#1d5055] font-medium"><i class="fas fa-briefcase mr-2"></i>Browse Jobs</a></li>
                    <li><a href="{{ url('courses') }}" class="text-[#0698ac] hover:text-[#1d5055] font-medium"><i class="fas fa-book-open mr-2"></i>View Courses</a></li>
                    <li><a href="{{ url('contact') }}" class="text-[#0698ac] hover:text-[#1d5055] font-medium"><i class="fas fa-phone-alt mr-2"></i>Contact Us</a></li>
                </ul>
                <div class="mt-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Contact Info</h3>
                    <p class="text-gray-600 text-sm"><i class="fas fa-phone-alt mr-2"></i>011-7186 2234</p>
                    <p class="text-gray-600 text-sm"><i class="fas fa-envelope mr-2"></i>info@taknikishiksha.org.in</p>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>
        </div>

        <!-- Footer -->
        <footer class="bg-gradient-to-r from-[#0698ac] to-[#731aa9] text-white py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- About -->
                    <div>
                        <h3 class="text-lg font-bold mb-4">About Takniki Shiksha</h3>
                        <p class="text-sm opacity-90">Official platform for yoga jobs, training, internships, and career opportunities. Join India's premier yoga and technical education ecosystem.</p>
                    </div>
                    <!-- Quick Links -->
                    <div>
                        <h3 class="text-lg font-bold mb-4">Quick Links</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="{{ url('services') }}" class="hover:text-gray-200">Services</a></li>
                            <li><a href="{{ url('courses') }}" class="hover:text-gray-200">Courses</a></li>
                            <li><a href="{{ url('careers') }}" class="hover:text-gray-200">Jobs</a></li>
                            <li><a href="{{ url('blogs') }}" class="hover:text-gray-200">Blog</a></li>
                            <li><a href="{{ url('contact') }}" class="hover:text-gray-200">Contact</a></li>
                        </ul>
                    </div>
                    <!-- Contact -->
                    <div>
                        <h3 class="text-lg font-bold mb-4">Contact Us</h3>
                        <p class="text-sm opacity-90"><i class="fas fa-phone-alt mr-2"></i>011-7186 2234</p>
                        <p class="text-sm opacity-90"><i class="fas fa-envelope mr-2"></i>info@taknikishiksha.org.in</p>
                        <p class="text-sm opacity-90"><i class="fas fa-map-marker-alt mr-2"></i>Delhi, India</p>
                    </div>
                </div>
                <div class="mt-8 border-t border-white/20 pt-4 text-center">
                    <p class="text-sm">&copy; {{ date('Y') }} Takniki Shiksha Vidhaan Council. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <!-- Overlay for Mobile Menu -->
        <div class="overlay hidden fixed inset-0 bg-black/50 z-40"></div>
    </div>

    <!-- Scripts -->
    @stack('scripts')
</body>
</html>