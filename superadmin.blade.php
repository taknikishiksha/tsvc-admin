<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Super Admin - Takniki Shiksha')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('photos/tsvc.png') }}">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#0698ac',
                    }
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Dropdown base - hidden by default; use .show class to display */
        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            padding: 1.5rem;
            z-index: 100;
            min-width: 800px;
            border: 1px solid #e5e7eb;
            margin-top: 8px;
            transition: opacity 160ms ease, transform 160ms ease;
            opacity: 0;
            transform: translateY(-6px);
            pointer-events: none;
        }
        /* Visible state */
        .dropdown-menu.show {
            display: block;
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .dark .dropdown-menu {
            background: #1f2937;
            border-color: #374151;
        }

        .dropdown-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        .dropdown-section {
            display: flex;
            flex-direction: column;
        }

        .dropdown-section-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #0698ac;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #0698ac;
            display: flex;
            align-items: center;
        }

        .dark .dropdown-section-title {
            color: #38bdf8;
            border-bottom-color: #38bdf8;
        }

        .dropdown-link {
            display: block;
            padding: 0.5rem 0;
            color: #374151;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            border-radius: 6px;
            padding-left: 0.5rem;
        }

        .dark .dropdown-link {
            color: #d1d5db;
        }

        .dropdown-link:hover {
            color: #0698ac;
            background: #f0f9ff;
            transform: translateX(4px);
        }

        .dark .dropdown-link:hover {
            background: #1e3a8a;
            color: #bfdbfe;
        }

        /* Mobile Menu Styling */
        .mobile-menu-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .mobile-menu-group {
            position: relative;
        }

        .mobile-submenu {
            transition: all 0.3s ease-in-out;
        }

        .mobile-submenu-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Smooth transitions for mobile menu */
        #mobileMenu {
            transition: transform 0.3s ease-in-out;
        }

        #mobileOverlay {
            transition: opacity 0.3s ease-in-out;
        }

        /* Dark mode support for mobile menu */
        .dark .mobile-menu-item:hover {
            background: #374151;
        }

        .dark .mobile-submenu-item:hover {
            background: #4b5563;
        }

        /* Ensure desktop menu is hidden on mobile */
        @media (max-width: 767px) {
            .dropdown-menu {
                display: none !important;
            }
            
            /* Ensure mobile menu is full height */
            #mobileMenu {
                height: 100vh;
            }
        }

        /* Smooth transitions */
        .bg-white {
            transition: background-color 0.3s ease;
        }

        .bg-gray-50 {
            transition: background-color 0.3s ease;
        }

        /* Custom scrollbar for dark mode */
        .dark ::-webkit-scrollbar {
            width: 8px;
        }

        .dark ::-webkit-scrollbar-track {
            background: #374151;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #6B7280;
            border-radius: 4px;
        }

        .dark ::-webkit-scrollbar-thumb:hover {
            background: #9CA3AF;
        }
    </style>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900">
    <!-- Super Admin Header -->
    <nav class="bg-white shadow-lg sticky top-0 z-50 dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Top Bar -->
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <img src="{{ asset('photos/tsvc.png') }}" alt="Takniki Shiksha Logo" class="h-12 w-12 rounded-lg">
                        <div class="absolute -bottom-1 -right-1 bg-green-500 text-white w-6 h-6 rounded-full flex items-center justify-center shadow-lg">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800 dark:text-white">Takniki Shiksha</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Super Admin Portal</p>
                    </div>
                </div>

                <!-- Desktop Menu Items -->
                <div class="hidden md:flex items-center space-x-6">
                    <!-- Quick Stats -->
                    <div class="flex items-center space-x-4">
                        <div class="text-center">
                            <div class="text-lg font-bold text-gray-800 dark:text-white">1,247</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Users</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-gray-800 dark:text-white">48</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Courses</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-gray-800 dark:text-white">156</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Queries</div>
                        </div>
                    </div>

                    <!-- Language Selector -->
                    <div class="relative" id="languageDropdown">
                        <button class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                            <i class="fas fa-globe"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 py-2 hidden z-50" id="languageMenu">
                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <span class="mr-2">🇺🇸</span> English
                            </a>
                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <span class="mr-2">🇮🇳</span> Hindi
                            </a>
                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <span class="mr-2">🇫🇷</span> French
                            </a>
                        </div>
                    </div>

                    <!-- Notifications -->
                    <div class="relative">
                        <button class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200 relative">
                            <i class="fas fa-bell"></i>
                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full"></span>
                        </button>
                    </div>

                    <!-- Theme Toggle -->
                    <button id="themeToggle" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                        <i class="fas fa-moon"></i>
                    </button>

                    <!-- User Profile Dropdown -->
                    <div class="relative" id="profileDropdown">
                        <button id="profileButton" class="flex items-center space-x-3 bg-gray-100 dark:bg-gray-700 rounded-lg px-4 py-2 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                            <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-semibold">
                                S
                            </div>
                            <div class="hidden sm:block text-left">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Shobhit Singh</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Super Admin</p>
                            </div>
                            <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                        </button>
                        
                        <!-- Profile Dropdown Menu -->
                        <div class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 py-2 hidden z-50" id="profileMenu">
                            <!-- Profile Header -->
                            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                                        S
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">Shobhit Singh</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">shobhitsingh@taknikishiksha.org.in</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Profile Options -->
                            <div class="py-2">
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-user-edit w-5 text-gray-400"></i>
                                    <span class="ml-3">Edit Profile</span>
                                </a>
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-camera w-5 text-gray-400"></i>
                                    <span class="ml-3">Change Photo</span>
                                </a>
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-lock w-5 text-gray-400"></i>
                                    <span class="ml-3">Change Password</span>
                                </a>
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-cog w-5 text-gray-400"></i>
                                    <span class="ml-3">Account Settings</span>
                                </a>
                            </div>
                            
                            <!-- System Options -->
                            <div class="py-2 border-t border-gray-200 dark:border-gray-700">
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-shield-alt w-5 text-gray-400"></i>
                                    <span class="ml-3">Security</span>
                                </a>
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-bell w-5 text-gray-400"></i>
                                    <span class="ml-3">Notifications</span>
                                </a>
                            </div>
                            
                            <!-- Logout Link -->
                            <div class="py-2 border-t border-gray-200 dark:border-gray-700">
                                <a href="#" 
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                   class="flex items-center px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                    <i class="fas fa-sign-out-alt w-5"></i>
                                    <span class="ml-3">Logout</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center space-x-2">
                    <!-- Mobile Theme Toggle -->
                    <button id="mobileThemeToggle" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                        <i class="fas fa-moon"></i>
                    </button>
                    
                    <!-- Mobile Language Toggle -->
                    <div class="relative" id="mobileLanguageDropdown">
                        <button class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                            <i class="fas fa-globe"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 py-2 hidden z-50" id="mobileLanguageMenu">
                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <span class="mr-2">🇺🇸</span> English
                            </a>
                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <span class="mr-2">🇮🇳</span> Hindi
                            </a>
                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <span class="mr-2">🇫🇷</span> French
                            </a>
                        </div>
                    </div>
                    
                    <button id="mobile-menu-button" class="text-gray-700 dark:text-gray-300 hover:text-primary focus:outline-none" type="button" aria-label="Open mobile menu">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Desktop Navigation Menu -->
            <div class="hidden md:block border-t border-gray-200 dark:border-gray-700"> 
                <ul class="flex space-x-8 py-3">
                    <!-- Dashboard -->
                    <li class="nav-item relative">
                        <a href="{{ route('superadmin.dashboard') }}" class="text-gray-700 dark:text-gray-300 hover:text-primary font-medium flex items-center {{ request()->routeIs('superadmin.dashboard') ? 'text-primary border-b-2 border-primary' : '' }}">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Dashboard
                        </a>
                    </li>

                    <!-- Management Dropdown -->
                    <li class="nav-item relative" data-has-dropdown>
                        <a href="#" class="text-gray-700 dark:text-gray-300 hover:text-primary font-medium flex items-center">
                            <i class="fas fa-cogs mr-2"></i>
                            Management
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </a>
                        <div class="dropdown-menu" aria-hidden="true" role="menu">
                            <div class="dropdown-grid">
                                <!-- User Management -->
                                <div class="dropdown-section">
                                    <h3 class="dropdown-section-title">
                                        <i class="fas fa-users-cog mr-2"></i>
                                        User Management
                                    </h3>
                                    <a href="{{ route('superadmin.users.index') }}" class="dropdown-link">All Users</a>
                                    <a href="{{ route('superadmin.users.create') }}" class="dropdown-link">Create Users</a>
                                    <a href="#" class="dropdown-link">Role Management</a>
                                    <a href="#" class="dropdown-link">Bulk Actions</a>
                                </div>
                                
                                <!-- Training Management -->
                                <div class="dropdown-section">
                                    <h3 class="dropdown-section-title">
                                        <i class="fas fa-graduation-cap mr-2"></i>
                                        Training Management
                                    </h3>
                                    <a href="#" class="dropdown-link">Program Schedule</a>
                                    <a href="#" class="dropdown-link">Course Registration</a>
                                    <a href="#" class="dropdown-link">Admissions</a>
                                    <a href="#" class="dropdown-link">Attendance</a>
                                    <a href="#" class="dropdown-link">LMS Content</a>
                                </div>
                                
                                <!-- Yoga Service Management -->
                                <div class="dropdown-section">
                                    <h3 class="dropdown-section-title">
                                        <i class="fas fa-hands mr-2"></i>
                                        Yoga Services
                                    </h3>
                                    <a href="#" class="dropdown-link">Service Queries</a>
                                    <a href="#" class="dropdown-link">Teacher Assignment</a>
                                    <a href="#" class="dropdown-link">Location Management</a>
                                    <a href="#" class="dropdown-link">Client Dashboard</a>
                                    <a href="#" class="dropdown-link">Volunteer Teachers</a>
                                </div>

                                <!-- Recruitment Management -->
                                <div class="dropdown-section">
                                    <h3 class="dropdown-section-title">
                                        <i class="fas fa-user-tie mr-2"></i>
                                        Recruitment
                                    </h3>
                                    <a href="#" class="dropdown-link">Job Postings</a>
                                    <a href="#" class="dropdown-link">Applications</a>
                                    <a href="#" class="dropdown-link">Exam Process</a>
                                    <a href="#" class="dropdown-link">HR Management</a>
                                    <a href="#" class="dropdown-link">Internships</a>
                                </div>

                                <!-- Financial Management -->
                                <div class="dropdown-section">
                                    <h3 class="dropdown-section-title">
                                        <i class="fas fa-rupee-sign mr-2"></i>
                                        Financial Management
                                    </h3>
                                    <a href="#" class="dropdown-link">Fees Collection</a>
                                    <a href="#" class="dropdown-link">Salary Management</a>
                                    <a href="#" class="dropdown-link">Donations</a>
                                    <a href="#" class="dropdown-link">Invoice Generation</a>
                                    <a href="#" class="dropdown-link">Tax Compliance</a>
                                </div>

                                <!-- Exam Management -->
                                <div class="dropdown-section">
                                    <h3 class="dropdown-section-title">
                                        <i class="fas fa-file-alt mr-2"></i>
                                        Exam Management
                                    </h3>
                                    <a href="#" class="dropdown-link">Online Exams</a>
                                    <a href="#" class="dropdown-link">Offline Exams</a>
                                    <a href="#" class="dropdown-link">Question Bank</a>
                                    <a href="#" class="dropdown-link">Certificates</a>
                                </div>
                            </div>
                        </div>
                    </li>

                    <!-- Content Management -->
                    <li class="nav-item relative" data-has-dropdown>
                        <a href="#" class="text-gray-700 dark:text-gray-300 hover:text-primary font-medium flex items-center">
                            <i class="fas fa-edit mr-2"></i>
                            Content
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </a>
                        <div class="dropdown-menu" aria-hidden="true" role="menu">
                            <div class="dropdown-grid">
                                <div class="dropdown-section">
                                    <h3 class="dropdown-section-title">Content Management</h3>
                                    <a href="#" class="dropdown-link">Blog Posts</a>
                                    <a href="#" class="dropdown-link">Events</a>
                                    <a href="#" class="dropdown-link">FAQs</a>
                                    <a href="#" class="dropdown-link">SEO Settings</a>
                                </div>
                                
                                <div class="dropdown-section">
                                    <h3 class="dropdown-section-title">Website Pages</h3>
                                    <a href="#" class="dropdown-link">Home Page</a>
                                    <a href="#" class="dropdown-link">About Us</a>
                                    <a href="#" class="dropdown-link">Contact Us</a>
                                    <a href="#" class="dropdown-link">Privacy Policy</a>
                                </div>
                            </div>
                        </div>
                    </li>

                    <!-- NGO Management -->
                    <li class="nav-item relative" data-has-dropdown>
                        <a href="#" class="text-gray-700 dark:text-gray-300 hover:text-primary font-medium flex items-center">
                            <i class="fas fa-hands-helping mr-2"></i>
                            NGO
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </a>
                        <div class="dropdown-menu" aria-hidden="true" role="menu">
                            <div class="dropdown-grid">
                                <div class="dropdown-section">
                                    <h3 class="dropdown-section-title">NGO Management</h3>
                                    <a href="#" class="dropdown-link">Donation Tracking</a>
                                    <a href="#" class="dropdown-link">Fund Utilization</a>
                                    <a href="#" class="dropdown-link">12AA/80G Management</a>
                                    <a href="#" class="dropdown-link">Volunteer Programs</a>
                                </div>
                                
                                <div class="dropdown-section">
                                    <h3 class="dropdown-section-title">Partner Management</h3>
                                    <a href="#" class="dropdown-link">Yoga Centers</a>
                                    <a href="#" class="dropdown-link">Collaborations</a>
                                    <a href="#" class="dropdown-link">Revenue Sharing</a>
                                </div>
                            </div>
                        </div>
                    </li>

                    <!-- System Settings -->
                    <li class="nav-item relative" data-has-dropdown>
                        <a href="#" class="text-gray-700 dark:text-gray-300 hover:text-primary font-medium flex items-center">
                            <i class="fas fa-cogs mr-2"></i>
                            System
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </a>
                        <div class="dropdown-menu" aria-hidden="true" role="menu">
                            <div class="dropdown-grid">
                                <div class="dropdown-section">
                                    <h3 class="dropdown-section-title">System Settings</h3>
                                    <a href="#" class="dropdown-link">General Settings</a>
                                    <a href="#" class="dropdown-link">Security</a>
                                    <a href="#" class="dropdown-link">Backup</a>
                                    <a href="#" class="dropdown-link">API Management</a>
                                </div>
                                
                                <div class="dropdown-section">
                                    <h3 class="dropdown-section-title">Analytics & Reports</h3>
                                    <a href="#" class="dropdown-link">User Analytics</a>
                                    <a href="#" class="dropdown-link">Financial Reports</a>
                                    <a href="#" class="dropdown-link">Performance Metrics</a>
                                    <a href="#" class="dropdown-link">Custom Reports</a>
                                </div>
                            </div>
                        </div>
                    </li>

                    <!-- View Website -->
                    <li>
                        <a href="{{ url('/') }}" target="_blank" class="text-gray-700 dark:text-gray-300 hover:text-primary font-medium flex items-center">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            View Website
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- Mobile Menu Overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" id="mobileOverlay"></div>

    <!-- Mobile Menu -->
    <div class="fixed inset-y-0 left-0 w-80 bg-white dark:bg-gray-800 shadow-xl transform -translate-x-full transition-transform duration-300 ease-in-out z-50" id="mobileMenu">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('photos/tsvc.png') }}" alt="Logo" class="h-10 w-10 rounded-lg">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white">Super Admin</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Menu</p>
                    </div>
                </div>
                <button id="close-mobile-menu" class="text-gray-500 dark:text-gray-400 hover:text-primary" aria-label="Close mobile menu">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile nav content mirrors desktop sections -->
        <nav class="p-4 space-y-2 overflow-y-auto h-[calc(100vh-120px)]">
    <!-- Quick Stats in Mobile -->
    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
        <div class="grid grid-cols-3 gap-4 text-center">
            <div>
                <div class="text-lg font-bold text-gray-800 dark:text-white">1,247</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Users</div>
            </div>
            <div>
                <div class="text-lg font-bold text-gray-800 dark:text-white">48</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Courses</div>
            </div>
            <div>
                <div class="text-lg font-bold text-gray-800 dark:text-white">156</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Queries</div>
            </div>
        </div>
    </div>

    <!-- Dashboard -->
    <a href="{{ route('superadmin.dashboard') }}" class="mobile-menu-item flex items-center space-x-3 py-3 px-4 rounded-lg transition-all duration-200 hover:bg-primary hover:text-white text-gray-700 dark:text-gray-300">
        <i class="fas fa-tachometer-alt w-5"></i>
        <span class="font-medium">Dashboard</span>
    </a>

    <!-- MANAGEMENT (2-level menu) -->
    <div class="mobile-menu-group">
        <!-- Level 1: Management -->
        <button class="mobile-menu-toggle w-full flex items-center justify-between py-3 px-4 rounded-lg transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
            <div class="flex items-center space-x-3">
                <i class="fas fa-cogs w-5"></i>
                <span class="font-medium">Management</span>
            </div>
            <i class="fas fa-chevron-down text-xs transition-transform duration-200"></i>
        </button>

        <!-- Level 2 Wrapper -->
        <div class="mobile-submenu hidden space-y-2 mt-1 ml-4">

            <!-- USER MANAGEMENT -->
            <div class="mobile-subgroup">
                <button class="mobile-subgroup-toggle flex items-center justify-between py-2 px-4 rounded-lg transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-gray-700 dark:text-gray-300">
                    <span class="flex items-center space-x-2">
                        <i class="fas fa-users-cog w-4"></i>
                        <span>User Management</span>
                    </span>
                    <i class="fas fa-chevron-down text-[10px]"></i>
                </button>
                <div class="mobile-subsubmenu hidden space-y-1 mt-1 ml-6">
                    <a href="{{ route('superadmin.users.index') }}" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm transition-all duration-200 hover:bg-primary hover:text-white text-gray-700 dark:text-gray-300">
                        All Users
                    </a>
                    <a href="{{ route('superadmin.users.create') }}" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm transition-all duration-200 hover:bg-primary hover:text-white text-gray-700 dark:text-gray-300">
                        Create Users
                    </a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm transition-all duration-200 hover:bg-primary hover:text-white text-gray-700 dark:text-gray-300">
                        Role Management
                    </a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm transition-all duration-200 hover:bg-primary hover:text-white text-gray-700 dark:text-gray-300">
                        Bulk Actions
                    </a>
                </div>
            </div>

            <!-- TRAINING MANAGEMENT -->
            <div class="mobile-subgroup">
                <button class="mobile-subgroup-toggle flex items-center justify-between py-2 px-4 rounded-lg transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-gray-700 dark:text-gray-300">
                    <span class="flex items-center space-x-2">
                        <i class="fas fa-graduation-cap w-4"></i>
                        <span>Training Management</span>
                    </span>
                    <i class="fas fa-chevron-down text-[10px]"></i>
                </button>
                <div class="mobile-subsubmenu hidden space-y-1 mt-1 ml-6">
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm transition-all duration-200 hover:bg-primary hover:text-white text-gray-700 dark:text-gray-300">Program Schedule</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm transition-all duration-200 hover:bg-primary hover:text-white text-gray-700 dark:text-gray-300">Course Registration</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm transition-all duration-200 hover:bg-primary hover:text-white text-gray-700 dark:text-gray-300">Admissions</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm transition-all duration-200 hover:bg-primary hover:text-white text-gray-700 dark:text-gray-300">Attendance</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm transition-all duration-200 hover:bg-primary hover:text-white text-gray-700 dark:text-gray-300">LMS Content</a>
                </div>
            </div>

            <!-- YOGA SERVICES -->
            <div class="mobile-subgroup">
                <button class="mobile-subgroup-toggle flex items-center justify-between py-2 px-4 rounded-lg transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-gray-700 dark:text-gray-300">
                    <span class="flex items-center space-x-2">
                        <i class="fas fa-hands w-4"></i>
                        <span>Yoga Services</span>
                    </span>
                    <i class="fas fa-chevron-down text-[10px]"></i>
                </button>
                <div class="mobile-subsubmenu hidden space-y-1 mt-1 ml-6">
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Service Queries</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Teacher Assignment</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Location Management</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Client Dashboard</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Volunteer Teachers</a>
                </div>
            </div>

            <!-- RECRUITMENT -->
            <div class="mobile-subgroup">
                <button class="mobile-subgroup-toggle flex items-center justify-between py-2 px-4 rounded-lg transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-gray-700 dark:text-gray-300">
                    <span class="flex items-center space-x-2">
                        <i class="fas fa-user-tie w-4"></i>
                        <span>Recruitment</span>
                    </span>
                    <i class="fas fa-chevron-down text-[10px]"></i>
                </button>
                <div class="mobile-subsubmenu hidden space-y-1 mt-1 ml-6">
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Job Postings</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Applications</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Exam Process</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">HR Management</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Internships</a>
                </div>
            </div>

            <!-- FINANCIAL MANAGEMENT -->
            <div class="mobile-subgroup">
                <button class="mobile-subgroup-toggle flex items-center justify-between py-2 px-4 rounded-lg transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-gray-700 dark:text-gray-300">
                    <span class="flex items-center space-x-2">
                        <i class="fas fa-rupee-sign w-4"></i>
                        <span>Financial Management</span>
                    </span>
                    <i class="fas fa-chevron-down text-[10px]"></i>
                </button>
                <div class="mobile-subsubmenu hidden space-y-1 mt-1 ml-6">
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Fees Collection</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Salary Management</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Donations</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Invoice Generation</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Tax Compliance</a>
                </div>
            </div>

            <!-- EXAM MANAGEMENT -->
            <div class="mobile-subgroup">
                <button class="mobile-subgroup-toggle flex items-center justify-between py-2 px-4 rounded-lg transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-gray-700 dark:text-gray-300">
                    <span class="flex items-center space-x-2">
                        <i class="fas fa-file-alt w-4"></i>
                        <span>Exam Management</span>
                    </span>
                    <i class="fas fa-chevron-down text-[10px]"></i>
                </button>
                <div class="mobile-subsubmenu hidden space-y-1 mt-1 ml-6">
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Online Exams</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Offline Exams</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Question Bank</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Certificates</a>
                </div>
            </div>

        </div>
    </div>

    <!-- CONTENT (2-level) -->
    <div class="mobile-menu-group">
        <button class="mobile-menu-toggle w-full flex items-center justify-between py-3 px-4 rounded-lg transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
            <div class="flex items-center space-x-3">
                <i class="fas fa-edit w-5"></i>
                <span class="font-medium">Content</span>
            </div>
            <i class="fas fa-chevron-down text-xs transition-transform duration-200"></i>
        </button>

        <div class="mobile-submenu hidden space-y-2 mt-1 ml-4">
            <!-- Content Management -->
            <div class="mobile-subgroup">
                <button class="mobile-subgroup-toggle flex items-center justify-between py-2 px-4 rounded-lg transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-gray-700 dark:text-gray-300">
                    <span>Content Management</span>
                    <i class="fas fa-chevron-down text-[10px]"></i>
                </button>
                <div class="mobile-subsubmenu hidden space-y-1 mt-1 ml-6">
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Blog Posts</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Events</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">FAQs</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">SEO Settings</a>
                </div>
            </div>

            <!-- Website Pages -->
            <div class="mobile-subgroup">
                <button class="mobile-subgroup-toggle flex items-center justify-between py-2 px-4 rounded-lg transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-gray-700 dark:text-gray-300">
                    <span>Website Pages</span>
                    <i class="fas fa-chevron-down text-[10px]"></i>
                </button>
                <div class="mobile-subsubmenu hidden space-y-1 mt-1 ml-6">
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Home Page</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">About Us</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Contact Us</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Privacy Policy</a>
                </div>
            </div>
        </div>
    </div>

    <!-- NGO (2-level) -->
    <div class="mobile-menu-group">
        <button class="mobile-menu-toggle w-full flex items-center justify-between py-3 px-4 rounded-lg transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
            <div class="flex items-center space-x-3">
                <i class="fas fa-hands-helping w-5"></i>
                <span class="font-medium">NGO</span>
            </div>
            <i class="fas fa-chevron-down text-xs transition-transform duration-200"></i>
        </button>

        <div class="mobile-submenu hidden space-y-2 mt-1 ml-4">
            <!-- NGO Management -->
            <div class="mobile-subgroup">
                <button class="mobile-subgroup-toggle flex items-center justify-between py-2 px-4 rounded-lg transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-gray-700 dark:text-gray-300">
                    <span>NGO Management</span>
                    <i class="fas fa-chevron-down text-[10px]"></i>
                </button>
                <div class="mobile-subsubmenu hidden space-y-1 mt-1 ml-6">
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Donation Tracking</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Fund Utilization</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">12AA/80G Management</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Volunteer Programs</a>
                </div>
            </div>

            <!-- Partner Management -->
            <div class="mobile-subgroup">
                <button class="mobile-subgroup-toggle flex items-center justify-between py-2 px-4 rounded-lg transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-gray-700 dark:text-gray-300">
                    <span>Partner Management</span>
                    <i class="fas fa-chevron-down text-[10px]"></i>
                </button>
                <div class="mobile-subsubmenu hidden space-y-1 mt-1 ml-6">
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Yoga Centers</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Collaborations</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Revenue Sharing</a>
                </div>
            </div>
        </div>
    </div>

    <!-- SYSTEM (2-level) -->
    <div class="mobile-menu-group">
        <button class="mobile-menu-toggle w-full flex items-center justify-between py-3 px-4 rounded-lg transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
            <div class="flex items-center space-x-3">
                <i class="fas fa-cogs w-5"></i>
                <span class="font-medium">System</span>
            </div>
            <i class="fas fa-chevron-down text-xs transition-transform duration-200"></i>
        </button>

        <div class="mobile-submenu hidden space-y-2 mt-1 ml-4">
            <!-- System Settings -->
            <div class="mobile-subgroup">
                <button class="mobile-subgroup-toggle flex items-center justify-between py-2 px-4 rounded-lg transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-gray-700 dark:text-gray-300">
                    <span>System Settings</span>
                    <i class="fas fa-chevron-down text-[10px]"></i>
                </button>
                <div class="mobile-subsubmenu hidden space-y-1 mt-1 ml-6">
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">General Settings</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Security</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Backup</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">API Management</a>
                </div>
            </div>

            <!-- Analytics & Reports -->
            <div class="mobile-subgroup">
                <button class="mobile-subgroup-toggle flex items-center justify-between py-2 px-4 rounded-lg transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-gray-700 dark:text-gray-300">
                    <span>Analytics & Reports</span>
                    <i class="fas fa-chevron-down text-[10px]"></i>
                </button>
                <div class="mobile-subsubmenu hidden space-y-1 mt-1 ml-6">
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">User Analytics</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Financial Reports</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Performance Metrics</a>
                    <a href="#" class="mobile-menu-item block py-2 px-4 rounded-lg text-sm hover:bg-primary hover:text-white">Custom Reports</a>
                </div>
            </div>
        </div>
    </div>

    <!-- View Website -->
    <a href="{{ url('/') }}" target="_blank" class="mobile-menu-item flex items-center space-x-3 py-3 px-4 rounded-lg transition-all duration-200 hover:bg-primary hover:text-white text-gray-700 dark:text-gray-300">
        <i class="fas fa-external-link-alt w-5"></i>
        <span class="font-medium">View Website</span>
    </a>

    <!-- Logout -->
    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
       class="mobile-menu-item flex items-center space-x-3 py-3 px-4 rounded-lg transition-all duration-200 hover:bg-red-500 hover:text-white text-red-600 dark:text-red-400 mt-4">
        <i class="fas fa-sign-out-alt w-5"></i>
        <span class="font-medium">Logout</span>
    </a>
</nav>

    <!-- Single Hidden Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // -----------------------
        // Profile Dropdown (Desktop)
        // -----------------------
        const profileDropdown = document.getElementById('profileDropdown');
        const profileButton = document.getElementById('profileButton');
        const profileMenu = document.getElementById('profileMenu');

        if (profileDropdown && profileMenu && profileButton) {
            profileButton.addEventListener('click', (e) => {
                e.stopPropagation();
                profileMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', () => {
                profileMenu.classList.add('hidden');
            });
        }

        // =========================
        // Language Dropdown (Desktop)
        // =========================
        const languageDropdown = document.getElementById('languageDropdown');
        const languageMenu = document.getElementById('languageMenu');
        
        if (languageDropdown && languageMenu) {
            languageDropdown.addEventListener('click', (e) => {
                e.stopPropagation();
                languageMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', () => {
                languageMenu.classList.add('hidden');
            });
        }

        // =========================
        // Language Dropdown (Mobile)
        // =========================
        const mobileLanguageDropdown = document.getElementById('mobileLanguageDropdown');
        const mobileLanguageMenu = document.getElementById('mobileLanguageMenu');
        
        if (mobileLanguageDropdown && mobileLanguageMenu) {
            mobileLanguageDropdown.addEventListener('click', (e) => {
                e.stopPropagation();
                mobileLanguageMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', () => {
                mobileLanguageMenu.classList.add('hidden');
            });
        }

        // =========================
        // Theme Toggle (Desktop + Mobile)
        // =========================
        const themeToggle = document.getElementById('themeToggle');
        const mobileThemeToggle = document.getElementById('mobileThemeToggle');
        
        function updateThemeIcons(isDark) {
            if (themeToggle) {
                const icon = themeToggle.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-moon', !isDark);
                    icon.classList.toggle('fa-sun', isDark);
                }
            }
            if (mobileThemeToggle) {
                const icon = mobileThemeToggle.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-moon', !isDark);
                    icon.classList.toggle('fa-sun', isDark);
                }
            }
        }

        function toggleTheme() {
            document.documentElement.classList.toggle('dark');
            const isDark = document.documentElement.classList.contains('dark');
            updateThemeIcons(isDark);
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }
        
        if (themeToggle) {
            themeToggle.addEventListener('click', toggleTheme);
        }
        
        if (mobileThemeToggle) {
            mobileThemeToggle.addEventListener('click', toggleTheme);
        }

        // Saved theme apply
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
            updateThemeIcons(true);
        }

        // =========================
        // Desktop Navigation Hover Dropdown (robust, with small hide-delay)
        // =========================
        const navItems = document.querySelectorAll('[data-has-dropdown]');
        navItems.forEach(item => {
            const dropdownMenu = item.querySelector('.dropdown-menu');
            let hideTimeout = null;

            if (!dropdownMenu) return;

            const show = () => {
                // close other open dropdowns
                document.querySelectorAll('.dropdown-menu.show').forEach(dm => {
                    if (dm !== dropdownMenu) {
                        dm.classList.remove('show');
                        dm.setAttribute('aria-hidden', 'true');
                    }
                });
                dropdownMenu.classList.add('show');
                dropdownMenu.setAttribute('aria-hidden', 'false');
            };

            const hide = () => {
                dropdownMenu.classList.remove('show');
                dropdownMenu.setAttribute('aria-hidden', 'true');
            };

            const clearHide = () => {
                if (hideTimeout) {
                    clearTimeout(hideTimeout);
                    hideTimeout = null;
                }
            };

            // show when mouse enters item or dropdown
            item.addEventListener('mouseenter', (e) => {
                clearHide();
                show();
            });

            dropdownMenu.addEventListener('mouseenter', (e) => {
                clearHide();
                show();
            });

            // start a small timer on leave (prevents accidental flicker)
            item.addEventListener('mouseleave', (e) => {
                clearHide();
                hideTimeout = setTimeout(hide, 200);
            });

            dropdownMenu.addEventListener('mouseleave', (e) => {
                clearHide();
                hideTimeout = setTimeout(hide, 200);
            });

            // also support keyboard focus (accessibility)
            const link = item.querySelector('a');
            if (link) {
                link.addEventListener('focus', show);
                link.addEventListener('blur', () => {
                    hideTimeout = setTimeout(hide, 200);
                });
            }
        });

        // =========================
        // Mobile Menu Functionality
        // =========================
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const closeMobileMenu = document.getElementById('close-mobile-menu');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileOverlay = document.getElementById('mobileOverlay');

        if (mobileMenuButton && closeMobileMenu && mobileMenu && mobileOverlay) {

            const closeMenu = () => {
                mobileMenu.classList.add('-translate-x-full');
                mobileOverlay.classList.add('hidden');
                document.body.style.overflow = 'auto';

                // Close all first-level submenus
                document.querySelectorAll('.mobile-submenu').forEach(submenu => {
                    submenu.classList.add('hidden');
                });

                // Reset all first-level chevrons
                document.querySelectorAll('.mobile-menu-toggle').forEach(toggle => {
                    const chevron = toggle.querySelector('.fa-chevron-up, .fa-chevron-down');
                    if (chevron) {
                        chevron.classList.remove('fa-chevron-up');
                        chevron.classList.add('fa-chevron-down');
                    }
                });

                // Close all second-level submenus
                document.querySelectorAll('.mobile-subsubmenu').forEach(subsubmenu => {
                    subsubmenu.classList.add('hidden');
                });

                // Reset all second-level chevrons
                document.querySelectorAll('.mobile-subgroup-toggle').forEach(btn => {
                    const chevron = btn.querySelector('.fa-chevron-up, .fa-chevron-down');
                    if (chevron) {
                        chevron.classList.remove('fa-chevron-up');
                        chevron.classList.add('fa-chevron-down');
                    }
                });
            };

            // Open mobile menu
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.remove('-translate-x-full');
                mobileOverlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });

            // Close buttons
            closeMobileMenu.addEventListener('click', closeMenu);
            mobileOverlay.addEventListener('click', closeMenu);

            // -------------------------
            // 1st Level: Management / Content / NGO / System
            // -------------------------
            const mobileMenuToggles = document.querySelectorAll('.mobile-menu-toggle');
            mobileMenuToggles.forEach(toggle => {
                toggle.addEventListener('click', (e) => {
                    const submenu = toggle.parentElement.querySelector('.mobile-submenu');
                    const chevron = toggle.querySelector('.fa-chevron-up, .fa-chevron-down');

                    // Close other first-level submenus
                    mobileMenuToggles.forEach(otherToggle => {
                        if (otherToggle !== toggle) {
                            const otherSubmenu = otherToggle.parentElement.querySelector('.mobile-submenu');
                            const otherChevron = otherToggle.querySelector('.fa-chevron-up, .fa-chevron-down');
                            if (otherSubmenu) otherSubmenu.classList.add('hidden');
                            if (otherChevron) {
                                otherChevron.classList.remove('fa-chevron-up');
                                otherChevron.classList.add('fa-chevron-down');
                            }
                        }
                    });

                    if (submenu) {
                        submenu.classList.toggle('hidden');
                    }
                    if (chevron) {
                        chevron.classList.toggle('fa-chevron-down');
                        chevron.classList.toggle('fa-chevron-up');
                    }
                });
            });

            // -------------------------
            // 2nd Level: User Management, Training, etc.
            // -------------------------
            const mobileSubgroupToggles = document.querySelectorAll('.mobile-subgroup-toggle');
            mobileSubgroupToggles.forEach(btn => {
                btn.addEventListener('click', () => {
                    const subsubmenu = btn.parentElement.querySelector('.mobile-subsubmenu');
                    const chevron = btn.querySelector('.fa-chevron-up, .fa-chevron-down');

                    if (subsubmenu) {
                        subsubmenu.classList.toggle('hidden');
                    }
                    if (chevron) {
                        chevron.classList.toggle('fa-chevron-down');
                        chevron.classList.toggle('fa-chevron-up');
                    }
                });
            });

            // Leaf items par click -> menu close
            const mobileMenuItems = document.querySelectorAll('.mobile-menu-item');
            mobileMenuItems.forEach(item => {
                item.addEventListener('click', () => {
                    // If link has href="#" don't navigate; but still close.
                    closeMenu();
                });
            });

            // Escape key se close
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !mobileMenu.classList.contains('-translate-x-full')) {
                    closeMenu();
                }
            });
        }
    });
</script>

</body>
</html>
