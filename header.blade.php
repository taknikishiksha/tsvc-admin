<!-- Navigation Bar -->
<nav class="bg-white shadow-lg sticky top-0 z-50">
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
                    <a href="{{ route('login') }}">
    <h1 class="text-xl font-bold text-gray-800 hover:text-primary transition-colors duration-200">Takniki Shiksha</h1>
</a>

                    <p class="text-xs text-gray-500">Training & Careers Portal</p>
                </div>
            </div>

            <!-- Desktop Search Bar -->
            @include('components.search-bar')

            <!-- Desktop Menu Items -->
            <div class="hidden md:flex items-center space-x-6">
                <!-- Language Switcher -->
                @include('components.language-switcher')
                
                @auth
                    <a href="{{ route('dashboard') }}" class="bg-[#0698ac] text-white px-6 py-2 rounded-lg hover:bg-[#1d5055] font-medium">Dashboard</a>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-gray-700 hover:text-red-600 font-medium">Logout</a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-[#0698ac] font-medium">Login</a>
                    <a href="{{ url('apply') }}" class="bg-[#0698ac] text-white px-6 py-2 rounded-lg hover:bg-[#0698ac] font-medium shadow-md">Apply Now</a>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-gray-700 hover:text-[#0698ac] focus:outline-none" type="button">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Search Bar -->
        <div id="mobile-search" class="py-3 border-t border-gray-200 md:hidden">
            @include('components.search-bar', ['mobile' => true])
        </div>

        <!-- Desktop Navigation Menu -->
        <div class="hidden md:block border-t border-gray-200">
            <ul class="flex space-x-8 py-3">
                <!-- Home -->
                <li class="nav-item">
                    <a href="{{ url('/') }}" class="text-gray-700 hover:text-[#0698ac] font-medium flex items-center {{ request()->is('/') ? 'text-[#0698ac] border-b-2 border-[#0698ac]' : '' }}">
                        Home
                    </a>
                </li>

                <!-- Courses -->
                <li class="nav-item">
                    <a href="#" class="text-gray-700 hover:text-[#0698ac] font-medium flex items-center">
                        Courses
                        <i class="fas fa-chevron-down ml-1 text-xs"></i>
                    </a>
                    <div class="dropdown-menu">
                        <div class="dropdown-grid">
                            <div class="dropdown-section">
                                <h3 class="dropdown-section-title">YCB Courses</h3>
                                <a href="{{ route('YCB-Level-1') }}" class="dropdown-link">YCB Level 1</a>
                                <a href="{{ route('YCB-Level-2') }}" class="dropdown-link">YCB Level 2</a>
                                <a href="{{ route('YCB-Level-3') }}" class="dropdown-link">YCB Level 3</a>
                            </div>
                            
                            <div class="dropdown-section">
                                <h3 class="dropdown-section-title">Workshops</h3>
                                <a href="{{ route('yoga-therapy') }}" class="dropdown-link">Yoga Therapy</a>
                                <a href="{{ route('meditation') }}" class="dropdown-link">Meditation</a>
                                <a href="{{ route('pranayama') }}" class="dropdown-link">Pranayama</a>
                            </div>
                            
                            <div class="dropdown-section">
                                <h3 class="dropdown-section-title">Short-Term Training</h3>
                                <a href="{{ route('beginner-yoga') }}" class="dropdown-link">Beginner Yoga</a>
                                <a href="{{ route('advanced-asanas') }}" class="dropdown-link">Advanced Asanas</a>
                                <a href="{{ route('yoga-for-kids') }}" class="dropdown-link">Yoga for Kids</a>
                            </div>
                            
                            <div class="dropdown-section">
                                <h3 class="dropdown-section-title">Certification</h3>
                                <a href="{{ route('RYT-200') }}" class="dropdown-link">RYT 200</a>
                                <a href="{{ route('RYT-500') }}" class="dropdown-link">RYT 500</a>
                                <a href="{{ route('specialist-courses') }}" class="dropdown-link">Specialist Courses</a>
                            </div>
                            
                            <div class="dropdown-section">
                                <h3 class="dropdown-section-title">Online Learning</h3>
                                <a href="{{ route('live-sessions') }}" class="dropdown-link">Live Sessions</a>
                                <a href="{{ route('recorded-classes') }}" class="dropdown-link">Recorded Classes</a>
                                <a href="{{ route('self-paced-courses') }}" class="dropdown-link">Self-Paced Courses</a>
                            </div>
                            
                            <div class="dropdown-section">
                                <h3 class="dropdown-section-title">Resources</h3>
                                <a href="{{ route('study-materials') }}" class="dropdown-link">Study Materials</a>
                                <a href="{{ route('practice-videos') }}" class="dropdown-link">Practice Videos</a>
                                <a href="{{ route('exam-preparation') }}" class="dropdown-link">Exam Preparation</a>
                            </div>
                        </div>
                    </div>
                </li>

                <!-- Services -->
                <li class="nav-item">
                    <a href="#" class="text-gray-700 hover:text-[#0698ac] font-medium flex items-center">
                        Services
                        <i class="fas fa-chevron-down ml-1 text-xs"></i>
                    </a>
                    <div class="dropdown-menu">
                        <div class="dropdown-grid">
                            <div class="dropdown-section">
                                <h3 class="dropdown-section-title">Class Types</h3>
                                <a href="{{ route('home-class') }}" class="dropdown-link">Home Classes</a>
                                <a href="{{ route('online-class') }}" class="dropdown-link">Online Classes</a>
                                <a href="{{ route('group-class') }}" class="dropdown-link">Group Classes</a>
                                <a href="{{ route('corporate-wellness') }}" class="dropdown-link">Corporate Wellness</a>
                            </div>
                            
                            <div class="dropdown-section">
                                <h3 class="dropdown-section-title">Specialized Services</h3>
                                <a href="{{ route('therapeutic-yoga') }}" class="dropdown-link">Therapeutic Yoga</a>
                                <a href="{{ route('prenatal-yoga') }}" class="dropdown-link">Prenatal Yoga</a>
                                <a href="{{ route('senior-yoga') }}" class="dropdown-link">Senior Yoga</a>
                                <a href="{{ route('sports-yoga') }}" class="dropdown-link">Sports Yoga</a>
                            </div>
                            
                            <div class="dropdown-section">
                                <h3 class="dropdown-section-title">Wellness Programs</h3>
                                <a href="{{ route('stress-management') }}" class="dropdown-link">Stress Management</a>
                                <a href="{{ route('weight-management') }}" class="dropdown-link">Weight Management</a>
                                <a href="{{ route('detox-programs') }}" class="dropdown-link">Detox Programs</a>
                                <a href="{{ route('mindfulness-training') }}" class="dropdown-link">Mindfulness Training</a>
                            </div>
                        </div>
                    </div>
                </li>

                <!-- Careers -->
                <li class="nav-item">
                    <a href="#" class="text-gray-700 hover:text-[#0698ac] font-medium flex items-center">
                        Careers
                        <i class="fas fa-chevron-down ml-1 text-xs"></i>
                    </a>
                    <div class="dropdown-menu">
                        <div class="dropdown-grid">
                            <div class="dropdown-section">
                                <h3 class="dropdown-section-title">Job Opportunities</h3>
                                <a href="{{ route('yoga-instructor') }}" class="dropdown-link">Yoga Instructor</a>
                                <a href="{{ route('therapy-specialist') }}" class="dropdown-link">Therapy Specialist</a>
                                <a href="{{ route('wellness-coach') }}" class="dropdown-link">Wellness Coach</a>
                                <a href="{{ route('admin-staff') }}" class="dropdown-link">Admin Staff</a>
                            </div>
                            
                            <div class="dropdown-section">
                                <h3 class="dropdown-section-title">Training & Development</h3>
                                <a href="{{ route('teacher-training') }}" class="dropdown-link">Teacher Training</a>
                                <a href="{{ route('skill-enhancement') }}" class="dropdown-link">Skill Enhancement</a>
                                <a href="{{ route('certification-program') }}" class="dropdown-link">Certification Programs</a>
                                <a href="{{ route('workshops') }}" class="dropdown-link">Workshops</a>
                            </div>
                            
                            <div class="dropdown-section">
                                <h3 class="dropdown-section-title">Other Opportunities</h3>
                                <a href="{{ route('internship') }}" class="dropdown-link">Internships</a>
                                <a href="{{ route('volunteer') }}" class="dropdown-link">Volunteer</a>
                                <a href="{{ route('part-time-position') }}" class="dropdown-link">Part-time Positions</a>
                                <a href="{{ route('freelance-opportunities') }}" class="dropdown-link">Freelance Opportunities</a>
                            </div>
                        </div>
                    </div>
                </li>

                <!-- About Us -->
                <li class="nav-item">
                    <a href="#" class="text-gray-700 hover:text-[#0698ac] font-medium flex items-center">
                        About Us
                        <i class="fas fa-chevron-down ml-1 text-xs"></i>
                    </a>
                    <div class="dropdown-menu">
                        <div class="dropdown-grid">
                            <div class="dropdown-section">
                                <h3 class="dropdown-section-title">Our Organization</h3>
                                <a href="{{ route('our-mission') }}" class="dropdown-link">Our Mission</a>
                                <a href="{{ route('our-vision') }}" class="dropdown-link">Our Vision</a>
                                <a href="{{ route('our-values') }}" class="dropdown-link">Our Values</a>
                                <a href="{{ route('history') }}" class="dropdown-link">History</a>
                            </div>
                            
                            <div class="dropdown-section">
                                <h3 class="dropdown-section-title">Our Team</h3>
                                <a href="{{ route('leadership') }}" class="dropdown-link">Leadership</a>
                                <a href="{{ route('instructor') }}" class="dropdown-link">Instructors</a>
                                <a href="{{ route('support-staff') }}" class="dropdown-link">Support Staff</a>
                                <a href="{{ route('advisory-board') }}" class="dropdown-link">Advisory Board</a>
                            </div>
                            
                            <div class="dropdown-section">
                                <h3 class="dropdown-section-title">Recognition</h3>
                                <a href="{{ route('certification') }}" class="dropdown-link">Certifications</a>
                                <a href="{{ route('accreditations') }}" class="dropdown-link">Accreditations</a>
                                <a href="{{ route('awards') }}" class="dropdown-link">Awards</a>
                                <a href="{{ route('partners') }}" class="dropdown-link">Partners</a>
                            </div>
                        </div>
                    </div>
                </li>
                
                <!-- Contact Us -->
                <li class="nav-item">
                    <a href="#" class="text-gray-700 hover:text-[#0698ac] font-medium flex items-center">
                        Contact Us
                        <i class="fas fa-chevron-down ml-1 text-xs"></i>
                    </a>
                    <div class="dropdown-menu">
                        <div class="dropdown-grid">
                            <div class="dropdown-section">
                                <h3 class="dropdown-section-title">General Enquiries</h3>
                                <a href="{{ route('general-enquiry')}}" class="dropdown-link">General Enquiries</a>
                                <a href="{{ route('course-information') }}" class="dropdown-link">Course Information</a>
                                <a href="{{ route('fees-structure') }}" class="dropdown-link">Fee Structure</a>
                                <a href="{{ route('admission-process') }}" class="dropdown-link">Admission Process</a>
                            </div>
                            
                            <div class="dropdown-section">
                                <h3 class="dropdown-section-title">Support</h3>
                                <a href="{{ route('complaints-feedback')}}" class="dropdown-link">Complaints & Feedback</a>
                                <a href="{{ route('technical-support') }}" class="dropdown-link">Technical Support</a>
                                <a href="{{ route('student-support') }}" class="dropdown-link">Student Support</a>
                                <a href="{{ route('faculty-support') }}" class="dropdown-link">Faculty Support</a>
                            </div>
                            
                            <div class="dropdown-section">
                                <h3 class="dropdown-section-title">Business</h3>
                                <a href="{{ route('recruitment-cell') }}" class="dropdown-link">Recruitment Cell</a>
                                <a href="{{ route('partnership-collaboration') }}" class="dropdown-link">Partnership & Collaboration</a>
                                <a href="{{ route('media-enquiry') }}" class="dropdown-link">Media Enquiries</a>
                                <a href="{{ route('vendor-registration') }}" class="dropdown-link">Vendor Registration</a>
                            </div>
                        </div>
                    </div>
                </li>

                <!-- Blog -->
                <li>
                    <a href="https://taknikishiksha.org.in/blogs" target="_blank" rel="noopener noreferrer" class="text-gray-700 hover:text-[#0698ac] font-medium">Blog</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Mobile Menu -->
<div class="mobile-menu">
    <div class="p-4 border-b flex justify-between items-center bg-white sticky top-0 z-10">
        <div class="flex items-center space-x-3">
            <img src="{{ asset('photos/tsvc.png') }}" alt="Takniki Shiksha Logo" class="h-10 w-10 rounded-lg" />
            <div>
                <h2 class="text-lg font-bold text-gray-800">Takniki Shiksha</h2>
                <p class="text-xs text-gray-500">Training & Careers Portal</p>
            </div>
        </div>
        <button id="close-mobile-menu" class="text-gray-500 hover:text-[#0698ac]">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>

    <div class="p-4">
        <!-- Search in Mobile Menu -->
        <div class="mb-6">
            @include('components.search-bar', ['mobile' => true])
        </div>

        <!-- Language Switcher in Mobile -->
        <div class="mb-6">
            <h3 class="font-medium text-gray-800 mb-2">Language</h3>
            <div class="flex space-x-2">
                <button class="flex-1 py-2 bg-[#0698ac] text-white text-sm rounded">English</button>
                <button class="flex-1 py-2 bg-gray-200 text-gray-700 text-sm rounded">हिन्दी</button>
            </div>
        </div>

<!-- Login Button linking to login page -->
<div class="mb-6">
    <a href="{{ route('login') }}" class="block w-full text-center bg-[#0698ac] text-white py-3 rounded-lg font-medium shadow-md hover:bg-[#0698ac]">
        Login
    </a>
</div>


        <!-- Mobile Navigation Menu -->
        <ul class="space-y-1">
            <!-- Home -->
            <li>
                <a href="{{ url('/') }}" class="block py-3 px-4 text-gray-700 hover:bg-blue-50 rounded-lg font-medium {{ request()->is('/') ? 'bg-blue-50 text-[#0698ac]' : '' }}">
                    <i class="fas fa-home mr-3 text-[#0698ac]"></i>Home
                </a>
            </li>

            <!-- Courses -->
            <li class="mobile-menu-item">
                <button class="mobile-menu-toggle w-full py-3 px-4 text-gray-700 hover:bg-blue-50 rounded-lg font-medium flex justify-between items-center">
                    <span><i class="fas fa-book-open mr-3 text-[#0698ac]"></i>Courses</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div class="mobile-submenu">
                    <div class="py-2">
                        <!-- YCB Courses -->
                        <button class="mobile-submenu-toggle w-full px-4 py-2 text-sm font-medium text-gray-800 border-l-2 border-[#0698ac] bg-blue-50 flex justify-between items-center">
                            YCB Courses
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="mobile-submenu-content">
                            <a href="{{ route('YCB-Level-1') }}" class="mobile-submenu-link">YCB Level 1</a>
                            <a href="{{ route('YCB-Level-2') }}" class="mobile-submenu-link">YCB Level 2</a>
                            <a href="{{ route('YCB-Level-3') }}" class="mobile-submenu-link">YCB Level 3</a>
                        </div>

                        <!-- Workshops -->
                        <button class="mobile-submenu-toggle w-full px-4 py-2 text-sm font-medium text-gray-800 border-l-2 border-[#0698ac] bg-blue-50 flex justify-between items-center mt-2">
                            Workshops
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="mobile-submenu-content">
                            <a href="{{ route('yoga-therapy') }}" class="mobile-submenu-link">Yoga Therapy</a>
                            <a href="{{ route('meditation') }}" class="mobile-submenu-link">Meditation</a>
                            <a href="{{ route('pranayama') }}" class="mobile-submenu-link">Pranayama</a>
                        </div>

                        <!-- Short-Term Training -->
                        <button class="mobile-submenu-toggle w-full px-4 py-2 text-sm font-medium text-gray-800 border-l-2 border-[#0698ac] bg-blue-50 flex justify-between items-center mt-2">
                            Short-Term Training
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="mobile-submenu-content">
                            <a href="{{ route('beginner-yoga') }}" class="mobile-submenu-link">Beginner Yoga</a>
                            <a href="{{ route('advanced-asanas') }}" class="mobile-submenu-link">Advanced Asanas</a>
                            <a href="{{ route('yoga-for-kids') }}" class="mobile-submenu-link">Yoga for Kids</a>
                        </div>

                        <!-- Certification -->
                        <button class="mobile-submenu-toggle w-full px-4 py-2 text-sm font-medium text-gray-800 border-l-2 border-[#0698ac] bg-blue-50 flex justify-between items-center mt-2">
                            Certification
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="mobile-submenu-content">
                            <a href="{{ route('RYT-200') }}" class="mobile-submenu-link">RYT 200</a>
                            <a href="{{ route('RYT-500') }}" class="mobile-submenu-link">RYT 500</a>
                            <a href="{{ route('specialist-courses') }}" class="mobile-submenu-link">Specialist Courses</a>
                        </div>

                        <!-- Online Learning -->
                        <button class="mobile-submenu-toggle w-full px-4 py-2 text-sm font-medium text-gray-800 border-l-2 border-[#0698ac] bg-blue-50 flex justify-between items-center mt-2">
                            Online Learning
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="mobile-submenu-content">
                            <a href="{{ route('live-sessions') }}" class="mobile-submenu-link">Live Sessions</a>
                            <a href="{{ route('recorded-classes') }}" class="mobile-submenu-link">Recorded Classes</a>
                            <a href="{{ route('self-paced-courses') }}" class="mobile-submenu-link">Self-Paced Courses</a>
                        </div>

                        <!-- Resources -->
                        <button class="mobile-submenu-toggle w-full px-4 py-2 text-sm font-medium text-gray-800 border-l-2 border-[#0698ac] bg-blue-50 flex justify-between items-center mt-2">
                            Resources
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="mobile-submenu-content">
                            <a href="{{ route('study-materials') }}" class="mobile-submenu-link">Study Materials</a>
                            <a href="{{ route('practice-videos') }}" class="mobile-submenu-link">Practice Videos</a>
                            <a href="{{ route('exam-preparation') }}" class="mobile-submenu-link">Exam Preparation</a>
                        </div>
                    </div>
                </div>
            </li>

            <!-- Services -->
            <li class="mobile-menu-item">
                <button class="mobile-menu-toggle w-full py-3 px-4 text-gray-700 hover:bg-blue-50 rounded-lg font-medium flex justify-between items-center">
                    <span><i class="fas fa-hands-helping mr-3 text-[#0698ac]"></i>Services</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div class="mobile-submenu">
                    <div class="py-2">
                        <!-- Class Types -->
                        <button class="mobile-submenu-toggle w-full px-4 py-2 text-sm font-medium text-gray-800 border-l-2 border-[#0698ac] bg-blue-50 flex justify-between items-center">
                            Class Types
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="mobile-submenu-content">
                            <a href="{{ route('home-class') }}" class="mobile-submenu-link">Home Classes</a>
                            <a href="{{ route('online-class') }}" class="mobile-submenu-link">Online Classes</a>
                            <a href="{{ route('group-class') }}" class="mobile-submenu-link">Group Classes</a>
                            <a href="{{ route('corporate-wellness') }}" class="mobile-submenu-link">Corporate Wellness</a>
                        </div>

                        <!-- Specialized Services -->
                        <button class="mobile-submenu-toggle w-full px-4 py-2 text-sm font-medium text-gray-800 border-l-2 border-[#0698ac] bg-blue-50 flex justify-between items-center mt-2">
                            Specialized Services
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="mobile-submenu-content">
                            <a href="{{ route('therapeutic-yoga') }}" class="mobile-submenu-link">Therapeutic Yoga</a>
                            <a href="{{ route('prenatal-yoga') }}" class="mobile-submenu-link">Prenatal Yoga</a>
                            <a href="{{ route('senior-yoga') }}" class="mobile-submenu-link">Senior Yoga</a>
                            <a href="{{ route('sports-yoga') }}" class="mobile-submenu-link">Sports Yoga</a>
                        </div>

                        <!-- Wellness Programs -->
                        <button class="mobile-submenu-toggle w-full px-4 py-2 text-sm font-medium text-gray-800 border-l-2 border-[#0698ac] bg-blue-50 flex justify-between items-center mt-2">
                            Wellness Programs
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="mobile-submenu-content">
                            <a href="{{ route('stress-management') }}" class="mobile-submenu-link">Stress Management</a>
                            <a href="{{ route('weight-management') }}" class="mobile-submenu-link">Weight Management</a>
                            <a href="{{ route('detox-programs') }}" class="mobile-submenu-link">Detox Programs</a>
                            <a href="{{ route('mindfulness-training') }}" class="mobile-submenu-link">Mindfulness Training</a>
                        </div>
                    </div>
                </div>
            </li>

            <!-- Careers -->
            <li class="mobile-menu-item">
                <button class="mobile-menu-toggle w-full py-3 px-4 text-gray-700 hover:bg-blue-50 rounded-lg font-medium flex justify-between items-center">
                    <span><i class="fas fa-briefcase mr-3 text-[#0698ac]"></i>Careers</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div class="mobile-submenu">
                    <div class="py-2">
                        <!-- Job Opportunities -->
                        <button class="mobile-submenu-toggle w-full px-4 py-2 text-sm font-medium text-gray-800 border-l-2 border-[#0698ac] bg-blue-50 flex justify-between items-center">
                            Job Opportunities
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="mobile-submenu-content">
                            <a href="{{ route('yoga-instructor') }}" class="mobile-submenu-link">Yoga Instructor</a>
                            <a href="{{ route('therapy-specialist') }}" class="mobile-submenu-link">Therapy Specialist</a>
                            <a href="{{ route('wellness-coach') }}" class="mobile-submenu-link">Wellness Coach</a>
                            <a href="{{ route('admin-staff') }}" class="mobile-submenu-link">Admin Staff</a>
                        </div>

                        <!-- Training & Development -->
                        <button class="mobile-submenu-toggle w-full px-4 py-2 text-sm font-medium text-gray-800 border-l-2 border-[#0698ac] bg-blue-50 flex justify-between items-center mt-2">
                            Training & Development
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="mobile-submenu-content">
                            <a href="{{ route('teacher-training') }}" class="mobile-submenu-link">Teacher Training</a>
                            <a href="{{ route('skill-enhancement') }}" class="mobile-submenu-link">Skill Enhancement</a>
                            <a href="{{ route('certification-program') }}" class="mobile-submenu-link">Certification Programs</a>
                            <a href="{{ route('workshops') }}" class="mobile-submenu-link">Workshops</a>
                        </div>

                        <!-- Other Opportunities -->
                        <button class="mobile-submenu-toggle w-full px-4 py-2 text-sm font-medium text-gray-800 border-l-2 border-[#0698ac] bg-blue-50 flex justify-between items-center mt-2">
                            Other Opportunities
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="mobile-submenu-content">
                            <a href="{{ route('internship') }}" class="mobile-submenu-link">Internships</a>
                            <a href="{{ route('volunteer') }}" class="mobile-submenu-link">Volunteer</a>
                            <a href="{{ route('part-time-position') }}" class="mobile-submenu-link">Part-time Positions</a>
                            <a href="{{ route('freelance-opportunities') }}" class="mobile-submenu-link">Freelance Opportunities</a>
                        </div>
                    </div>
                </div>
            </li>

            <!-- About Us -->
            <li class="mobile-menu-item">
                <button class="mobile-menu-toggle w-full py-3 px-4 text-gray-700 hover:bg-blue-50 rounded-lg font-medium flex justify-between items-center">
                    <span><i class="fas fa-info-circle mr-3 text-[#0698ac]"></i>About Us</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div class="mobile-submenu">
                    <div class="py-2">
                        <!-- Our Organization -->
                        <button class="mobile-submenu-toggle w-full px-4 py-2 text-sm font-medium text-gray-800 border-l-2 border-[#0698ac] bg-blue-50 flex justify-between items-center">
                            Our Organization
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="mobile-submenu-content">
                            <a href="{{ route('our-mission') }}" class="mobile-submenu-link">Our Mission</a>
                            <a href="{{ route('our-vision') }}" class="mobile-submenu-link">Our Vision</a>
                            <a href="{{ route('our-values') }}" class="mobile-submenu-link">Our Values</a>
                            <a href="{{ route('history') }}" class="mobile-submenu-link">History</a>
                        </div>

                        <!-- Our Team -->
                        <button class="mobile-submenu-toggle w-full px-4 py-2 text-sm font-medium text-gray-800 border-l-2 border-[#0698ac] bg-blue-50 flex justify-between items-center mt-2">
                            Our Team
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="mobile-submenu-content">
                            <a href="{{ route('leadership') }}" class="mobile-submenu-link">Leadership</a>
                            <a href="{{ route('instructor') }}" class="mobile-submenu-link">Instructors</a>
                            <a href="{{ route('support-staff') }}" class="mobile-submenu-link">Support Staff</a>
                            <a href="{{ route('advisory-board') }}" class="mobile-submenu-link">Advisory Board</a>
                        </div>

                        <!-- Recognition -->
                        <button class="mobile-submenu-toggle w-full px-4 py-2 text-sm font-medium text-gray-800 border-l-2 border-[#0698ac] bg-blue-50 flex justify-between items-center mt-2">
                            Recognition
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="mobile-submenu-content">
                            <a href="{{ route('certification') }}" class="mobile-submenu-link">Certifications</a>
                            <a href="{{ route('accreditations') }}" class="mobile-submenu-link">Accreditations</a>
                            <a href="{{ route('awards') }}" class="mobile-submenu-link">Awards</a>
                            <a href="{{ route('partners') }}" class="mobile-submenu-link">Partners</a>
                        </div>
                    </div>
                </div>
            </li>

            <!-- Contact Us -->
            <li class="mobile-menu-item">
                <button class="mobile-menu-toggle w-full py-3 px-4 text-gray-700 hover:bg-blue-50 rounded-lg font-medium flex justify-between items-center">
                    <span><i class="fas fa-envelope mr-3 text-[#0698ac]"></i>Contact Us</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div class="mobile-submenu">
                    <div class="py-2">
                        <!-- General Enquiries -->
                        <button class="mobile-submenu-toggle w-full px-4 py-2 text-sm font-medium text-gray-800 border-l-2 border-[#0698ac] bg-blue-50 flex justify-between items-center">
                            General Enquiries
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="mobile-submenu-content">
                            <a href="{{ url('/general-enquiry') }}" class="mobile-submenu-link">General Enquiries</a>
                            <a href="{{ route('course-information') }}" class="mobile-submenu-link">Course Information</a>
                            <a href="{{ route('fees-structure') }}" class="mobile-submenu-link">Fee Structure</a>
                            <a href="{{ route('admission-process') }}" class="mobile-submenu-link">Admission Process</a>
                        </div>

                        <!-- Support -->
                        <button class="mobile-submenu-toggle w-full px-4 py-2 text-sm font-medium text-gray-800 border-l-2 border-[#0698ac] bg-blue-50 flex justify-between items-center mt-2">
                            Support
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="mobile-submenu-content">
                            <a href="{{ route('complaints-feedback') }}" class="mobile-submenu-link">Complaints & Feedback</a>
                            <a href="{{ route('technical-support') }}" class="mobile-submenu-link">Technical Support</a>
                            <a href="{{ route('student-support') }}" class="mobile-submenu-link">Student Support</a>
                            <a href="{{ route('faculty-support') }}" class="mobile-submenu-link">Faculty Support</a>
                        </div>

                        <!-- Business -->
                        <button class="mobile-submenu-toggle w-full px-4 py-2 text-sm font-medium text-gray-800 border-l-2 border-[#0698ac] bg-blue-50 flex justify-between items-center mt-2">
                            Business
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="mobile-submenu-content">
                            <a href="{{ route('recruitment-cell') }}" class="mobile-submenu-link">Recruitment Cell</a>
                            <a href="{{ route('partnership-collaboration') }}" class="mobile-submenu-link">Partnership & Collaboration</a>
                            <a href="{{ route('media-enquiry') }}" class="mobile-submenu-link">Media Enquiries</a>
                            <a href="{{ route('vendor-registration') }}" class="mobile-submenu-link">Vendor Registration</a>
                        </div>
                    </div>
                </div>
            </li>

            <!-- Blog -->
            <li>
                <a href="https://taknikishiksha.org.in/blogs" target="_blank" rel="noopener noreferrer" class="block py-3 px-4 text-gray-700 hover:bg-blue-50 rounded-lg font-medium">
                    <i class="fas fa-blog mr-3 text-[#0698ac]"></i>Blog
                </a>
            </li>
        </ul>

        <!-- Apply Now Button -->
        <div class="mt-8 pt-4 border-t">
            <a href="{{ url('apply') }}" class="block w-full text-center bg-[#0698ac] text-white py-3 rounded-lg font-medium shadow-md hover:bg-[#0698ac]">
                Apply Now
            </a>
        </div>
    </div>
</div>

<!-- Overlay -->
<div class="overlay"></div>

<style>
/* Mobile Menu Styling */
.mobile-menu {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: white;
    z-index: 100;
    overflow-y: auto;
}

.mobile-menu.active {
    display: block;
}

.overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 90;
}

.overlay.active {
    display: block;
}

/* Mobile Submenu Styling */
.mobile-submenu {
    display: none;
    padding-left: 1rem; /* Indent submenu for hierarchy */
}

.mobile-submenu.active {
    display: block;
}

/* Mobile Submenu Toggle (Category Titles) */
.mobile-submenu-toggle {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    text-align: left;
    background: #e0f7fa; /* Slightly different background for category titles */
    border-left: 2px solid #0698ac;
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
    font-weight: 500; /* Medium weight */
    color: #374151;
}

.mobile-submenu-toggle:hover {
    background: #b3e5fc;
}

/* Mobile Submenu Content (Options) */
.mobile-submenu-content {
    display: none; /* Hidden by default */
    padding-left: 1rem; /* Further indent options */
}

.mobile-submenu-content.active {
    display: block;
}

/* Mobile Submenu Links */
.mobile-submenu-link {
    display: block; /* Stack links vertically */
    padding: 0.5rem 1rem;
    color: #374151;
    text-decoration: none;
    font-size: 0.9rem;
}

.mobile-submenu-link:hover {
    background: #f0f9ff;
    color: #0698ac;
    border-radius: 4px;
}

/* Desktop Dropdown Menu */
.nav-item {
    position: relative;
}

.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    padding: 1rem;
    z-index: 100;
    min-width: 600px; /* Adjust as needed */
}

.nav-item:hover .dropdown-menu {
    display: block;
}

.dropdown-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* Creates a 3-column grid */
    gap: 1rem; /* Space between grid items */
}

.dropdown-section {
    display: flex;
    flex-direction: column;
}

.dropdown-section-title {
    font-size: 1rem;
    font-weight: bold;
    color: #0698ac;
    margin-bottom: 0.5rem;
}

.dropdown-link {
    display: block;
    padding: 0.5rem 0;
    color: #374151;
    text-decoration: none;
    font-size: 0.9rem;
}

.dropdown-link:hover {
    color: #0698ac;
    background: #f0f9ff;
    border-radius: 4px;
}

/* Ensure desktop menu is hidden on mobile */
@media (max-width: 767px) {
    .dropdown-menu {
        display: none !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const closeMobileMenuButton = document.getElementById('close-mobile-menu');
    const mobileMenu = document.querySelector('.mobile-menu');
    const overlay = document.querySelector('.overlay');
    const mobileMenuToggles = document.querySelectorAll('.mobile-menu-toggle');
    const mobileSubmenuToggles = document.querySelectorAll('.mobile-submenu-toggle');

    // Toggle main mobile menu and overlay
    mobileMenuButton.addEventListener('click', (e) => {
        e.preventDefault();
        mobileMenu.classList.toggle('active');
        overlay.classList.toggle('active');
    });

    closeMobileMenuButton.addEventListener('click', (e) => {
        e.preventDefault();
        mobileMenu.classList.remove('active');
        overlay.classList.remove('active');
        // Close all submenus and submenu content
        mobileMenuToggles.forEach(toggle => {
            const submenu = toggle.nextElementSibling;
            submenu.classList.remove('active');
            const chevron = toggle.querySelector('.fa-chevron-down');
            if (chevron) {
                chevron.classList.remove('fa-chevron-up');
                chevron.classList.add('fa-chevron-down');
            }
        });
        mobileSubmenuToggles.forEach(toggle => {
            const submenuContent = toggle.nextElementSibling;
            submenuContent.classList.remove('active');
            const chevron = toggle.querySelector('.fa-chevron-down');
            if (chevron) {
                chevron.classList.remove('fa-chevron-up');
                chevron.classList.add('fa-chevron-down');
            }
        });
    });

    overlay.addEventListener('click', () => {
        mobileMenu.classList.remove('active');
        overlay.classList.remove('active');
        // Close all submenus and submenu content
        mobileMenuToggles.forEach(toggle => {
            const submenu = toggle.nextElementSibling;
            submenu.classList.remove('active');
            const chevron = toggle.querySelector('.fa-chevron-down');
            if (chevron) {
                chevron.classList.remove('fa-chevron-up');
                chevron.classList.add('fa-chevron-down');
            }
        });
        mobileSubmenuToggles.forEach(toggle => {
            const submenuContent = toggle.nextElementSibling;
            submenuContent.classList.remove('active');
            const chevron = toggle.querySelector('.fa-chevron-down');
            if (chevron) {
                chevron.classList.remove('fa-chevron-up');
                chevron.classList.add('fa-chevron-down');
            }
        });
    });

    // Toggle main menu items (e.g., Courses, Services)
    mobileMenuToggles.forEach(toggle => {
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            const submenu = toggle.nextElementSibling;
            const chevron = toggle.querySelector('.fa-chevron-down');
            const isActive = submenu.classList.contains('active');

            // Close all other main menu submenus
            mobileMenuToggles.forEach(otherToggle => {
                if (otherToggle !== toggle) {
                    const otherSubmenu = otherToggle.nextElementSibling;
                    otherSubmenu.classList.remove('active');
                    const otherChevron = otherToggle.querySelector('.fa-chevron-down');
                    if (otherChevron) {
                        otherChevron.classList.remove('fa-chevron-up');
                        otherChevron.classList.add('fa-chevron-down');
                    }
                }
            });

            // Toggle the current submenu
            submenu.classList.toggle('active');
            if (chevron) {
                chevron.classList.toggle('fa-chevron-up');
                chevron.classList.toggle('fa-chevron-down');
            }

            // Close all nested submenu content if closing the main submenu
            if (!isActive) {
                submenu.querySelectorAll('.mobile-submenu-content').forEach(content => {
                    content.classList.remove('active');
                });
                submenu.querySelectorAll('.mobile-submenu-toggle .fa-chevron-down').forEach(icon => {
                    icon.classList.remove('fa-chevron-up');
                    icon.classList.add('fa-chevron-down');
                });
            }
        });
    });

    // Toggle submenu categories (e.g., YCB Courses, Workshops)
    mobileSubmenuToggles.forEach(toggle => {
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            const submenuContent = toggle.nextElementSibling;
            const chevron = toggle.querySelector('.fa-chevron-down');
            submenuContent.classList.toggle('active');
            if (chevron) {
                chevron.classList.toggle('fa-chevron-up');
                chevron.classList.toggle('fa-chevron-down');
            }
        });
    });
});
</script>