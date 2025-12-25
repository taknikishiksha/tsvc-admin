<!DOCTYPE html>
<html lang="en">
<head>
    <!-- ========== SEO & Meta ========== -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Takniki Shiksha Careers - Admin Dashboard for Global Education, Yoga & Wellness Network">
    <meta name="keywords" content="TSVC, Yoga, Careers, Admin Dashboard, Takniki Shiksha Vidhaan Council">
    <meta name="author" content="Takniki Shiksha Vidhaan Council">
    <meta property="og:image" content="{{ asset('photos/tsvc.png') }}">
    <meta property="og:title" content="TSVC Admin Dashboard">
    <meta property="og:description" content="World-class administration dashboard for Takniki Shiksha Careers">
    <meta property="og:type" content="website">

    <title>@yield('title','Admin Dashboard')</title>
    
    <!-- ====== Favicon for all browsers ====== -->
<link rel="icon" type="image/png" href="{{ asset('photos/tsvc.png') }}">

<!-- ====== Apple / iOS Support ====== -->
<link rel="apple-touch-icon" href="{{ asset('photos/tsvc.png') }}">

<!-- ====== Windows Tile (optional) ====== -->
<meta name="msapplication-TileImage" content="{{ asset('photos/tsvc.png') }}">

<!-- ====== Theme Color for Browser UI (optional) ====== -->
<meta name="theme-color" content="#0f172a">


    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
    /* ======= CSS VARIABLES & THEME ======= */
    :root {
        /* Professional Color Palette */
        --color-primary: #2563eb;
        --color-primary-dark: #1d4ed8;
        --color-secondary: #64748b;
        --color-success: #10b981;
        --color-warning: #f59e0b;
        --color-danger: #ef4444;
        --color-dark: #0f172a;
        --color-light: #f8fafc;
        --color-border: #e2e8f0;

        /* Typography */
        --font-family-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        --font-size-xs: 0.75rem;
        --font-size-sm: 0.875rem;
        --font-size-base: 1rem;
        --font-size-lg: 1.125rem;
        --font-size-xl: 1.25rem;
        --font-weight-normal: 400;
        --font-weight-medium: 500;
        --font-weight-semibold: 600;
        --font-weight-bold: 700;

        /* Spacing */
        --space-1: 0.25rem;
        --space-2: 0.5rem;
        --space-3: 0.75rem;
        --space-4: 1rem;
        --space-6: 1.5rem;
        --space-8: 2rem;

        /* Border Radius */
        --radius-sm: 0.375rem;
        --radius-md: 0.5rem;
        --radius-lg: 0.75rem;

        /* Shadows */
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);

        /* Transitions */
        --transition-fast: 150ms ease-in-out;
        --transition-base: 250ms ease-in-out;
    }

    /* Apply font family */
    body {
        font-family: var(--font-family-sans);
        font-weight: var(--font-weight-normal);
        line-height: 1.5;
        color: #1e293b;
        background-color: #f1f5f9; /* Lighter, modern background */
        overflow-x: hidden;
    }

    /* ======= ENHANCED TOP NAVBAR ======= */
    .topbar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 64px;
        display: flex;
        align-items: center;
        padding: 0 var(--space-6);
        background: #ffffff;
        border-bottom: 1px solid var(--color-border);
        box-shadow: var(--shadow-sm);
        z-index: 1030;
        transition: all var(--transition-base);
    }

    .topbar-logo {
        display: flex;
        align-items: center;
        gap: var(--space-3);
    }

    .topbar-logo img {
        height: 36px;
        width: auto;
        border-radius: var(--radius-sm);
    }

    .topbar-logo span {
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-semibold);
        color: var(--color-dark);
        letter-spacing: -0.025em;
    }

    .menu-toggle {
        background: none;
        border: none;
        font-size: 1.25rem;
        color: var(--color-secondary);
        cursor: pointer;
        padding: var(--space-2);
        border-radius: var(--radius-sm);
        transition: all var(--transition-fast);
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
    }

    .menu-toggle:hover {
        background-color: #f1f5f9;
        color: var(--color-primary);
    }

    /* ======= PROFESSIONAL SIDEBAR ======= */
    .sidebar {
        width: 260px;
        background: linear-gradient(180deg, var(--color-dark), #020617);
        min-height: calc(100vh - 64px);
        height: calc(100vh - 64px);
        position: fixed;
        color: #fff;
        overflow-y: auto;
        overflow-x: hidden;
        top: 64px;
        transition: all var(--transition-base);
        z-index: 1020;
        left: 0;
        display: flex;
        flex-direction: column;
        border-right: 1px solid rgba(255, 255, 255, 0.05);
    }

    .sidebar.hide-sidebar {
        margin-left: -260px;
    }

    .sidebar .brand {
        padding: var(--space-6) var(--space-4);
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        margin-bottom: var(--space-2);
    }

    .sidebar .brand img {
        height: 48px;
        width: auto;
        margin-bottom: var(--space-2);
        border-radius: var(--radius-sm);
    }

    .sidebar .brand small {
        font-size: var(--font-size-xs);
        color: rgba(255, 255, 255, 0.6);
        font-weight: var(--font-weight-medium);
        letter-spacing: 0.05em;
    }

    /* Sidebar Menu Items */
    .sidebar-menu {
        padding: var(--space-4) var(--space-3);
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        gap: var(--space-1);
    }

    .sidebar a {
        color: #cbd5e1;
        padding: var(--space-3) var(--space-4);
        display: flex;
        align-items: center;
        gap: var(--space-3);
        text-decoration: none;
        font-size: var(--font-size-sm);
        border-radius: var(--radius-md);
        transition: all var(--transition-fast);
        font-weight: var(--font-weight-medium);
        position: relative;
    }

    .sidebar a i {
        font-size: 1.125rem;
        width: 20px;
        text-align: center;
        opacity: 0.8;
    }

    .sidebar a:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
        padding-left: var(--space-6);
    }

    .sidebar a:hover i {
        opacity: 1;
    }

    .sidebar a.active {
        background: var(--color-primary);
        color: #fff;
        font-weight: var(--font-weight-semibold);
        box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
    }

    .sidebar a.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 60%;
        background: #fff;
        border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
    }

    .menu-title {
        padding: var(--space-6) var(--space-4) var(--space-2);
        font-size: var(--font-size-xs);
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.4);
        font-weight: var(--font-weight-semibold);
        letter-spacing: 0.05em;
        margin-top: var(--space-2);
    }

    .menu-title:first-child {
        margin-top: 0;
    }

    /* Submenu Styling */
    .submenu {
        padding-left: var(--space-2);
        margin-top: var(--space-1);
    }

    .submenu a {
        padding: var(--space-2) var(--space-4) var(--space-2) calc(var(--space-4) + 28px);
        font-size: var(--font-size-sm);
        color: rgba(203, 213, 225, 0.9);
        background: transparent;
    }

    .submenu a:hover {
        background: rgba(255, 255, 255, 0.05);
        color: #fff;
    }

    .submenu a.active {
        background: rgba(37, 99, 235, 0.15);
        color: #fff;
    }

    .submenu .disabled {
        color: rgba(255, 255, 255, 0.25);
        cursor: not-allowed;
        opacity: 0.6;
    }

    /* Collapse indicators */
    .sidebar a[data-bs-toggle="collapse"]::after {
        content: '\f107';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        margin-left: auto;
        transition: transform var(--transition-fast);
        font-size: 0.875rem;
        opacity: 0.7;
    }

    .sidebar a[data-bs-toggle="collapse"][aria-expanded="true"]::after {
        transform: rotate(180deg);
        opacity: 1;
    }

    /* ======= MODERN MAIN CONTENT AREA ======= */
    .main {
        margin-left: 260px;
        padding: 80px var(--space-6) var(--space-6);
        transition: all var(--transition-base);
        min-height: calc(100vh - 64px);
    }

    .main.full-width {
        margin-left: 0;
    }

    /* Content Header */
    .content-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--space-6);
        padding-bottom: var(--space-4);
        border-bottom: 1px solid var(--color-border);
    }

    .content-header h1 {
        font-size: var(--font-size-xl);
        font-weight: var(--font-weight-bold);
        color: var(--color-dark);
        margin: 0;
        line-height: 1.2;
    }

    /* ======= DASHBOARD CARD SYSTEM ======= */
    /* This is a framework for your dashboard home page */
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: var(--space-6);
        margin-top: var(--space-6);
    }

    .stat-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: var(--space-6);
        border: 1px solid var(--color-border);
        transition: all var(--transition-base);
        box-shadow: var(--shadow-sm);
    }

    .stat-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
        border-color: var(--color-primary);
    }

    .stat-card-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: var(--space-4);
        font-size: 1.25rem;
    }

    .stat-card-icon.primary {
        background: rgba(37, 99, 235, 0.1);
        color: var(--color-primary);
    }

    .stat-card-icon.success {
        background: rgba(16, 185, 129, 0.1);
        color: var(--color-success);
    }

    .stat-card-icon.warning {
        background: rgba(245, 158, 11, 0.1);
        color: var(--color-warning);
    }

    .stat-card-value {
        font-size: 1.875rem;
        font-weight: var(--font-weight-bold);
        color: var(--color-dark);
        line-height: 1;
        margin-bottom: var(--space-1);
    }

    .stat-card-label {
        font-size: var(--font-size-sm);
        color: var(--color-secondary);
        font-weight: var(--font-weight-medium);
        margin-bottom: var(--space-3);
    }

    .stat-card-change {
        font-size: var(--font-size-xs);
        font-weight: var(--font-weight-semibold);
        padding: var(--space-1) var(--space-2);
        border-radius: var(--radius-sm);
        display: inline-flex;
        align-items: center;
        gap: var(--space-1);
    }

    .stat-card-change.positive {
        background: rgba(16, 185, 129, 0.1);
        color: var(--color-success);
    }

    .stat-card-change.negative {
        background: rgba(239, 68, 68, 0.1);
        color: var(--color-danger);
    }

    /* ======= RESPONSIVE DESIGN ======= */
    @media (max-width: 1200px) {
        .dashboard-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        }
    }

    @media (max-width: 992px) {
        .sidebar {
            left: -260px;
            margin-left: 0;
            height: calc(100vh - 64px);
            position: fixed;
            top: 64px;
            box-shadow: var(--shadow-lg);
        }

        .sidebar.show {
            left: 0;
        }

        /* Mobile overlay backdrop */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 64px;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1019;
            backdrop-filter: blur(2px);
        }

        .sidebar-backdrop.show {
            display: block;
        }

        .main {
            margin-left: 0;
            padding-top: 80px;
            padding-left: var(--space-4);
            padding-right: var(--space-4);
        }

        .content-header {
            flex-direction: column;
            align-items: flex-start;
            gap: var(--space-4);
        }

        .dashboard-grid {
            grid-template-columns: 1fr;
            gap: var(--space-4);
        }
    }

    @media (max-width: 768px) {
        .topbar {
            padding: 0 var(--space-4);
            height: 60px;
        }

        .sidebar {
            top: 60px;
            height: calc(100vh - 60px);
        }

        .main {
            padding-top: 76px;
        }

        .stat-card {
            padding: var(--space-4);
        }

        .stat-card-value {
            font-size: 1.5rem;
        }
    }

    /* ======= UTILITY CLASSES ======= */
    .text-muted {
        color: var(--color-secondary);
    }

    .text-primary {
        color: var(--color-primary);
    }

    .fw-semibold {
        font-weight: var(--font-weight-semibold);
    }

    .mb-4 {
        margin-bottom: var(--space-6);
    }

    .mt-4 {
        margin-top: var(--space-6);
    }
</style>
    @stack('styles')
</head>

<body>

<!-- ========== TOP NAVBAR (ADDED WITHOUT REMOVING ANYTHING) ========== -->
<div class="topbar">
    <button class="menu-toggle" id="menuBtn">
        <i class="fas fa-bars"></i>
    </button>

    <div class="topbar-logo d-flex align-items-center">
        <img src="{{ asset('photos/tsvc.png') }}" alt="TSVC">
        <span class="ms-2 fw-semibold">TSVC Admin Panel</span>
    </div>

    <div class="ms-auto">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-sm btn-outline-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</div>


<!-- ========== ORIGINAL SIDEBAR (UNCHANGED CONTENT) ========== -->
<div class="sidebar" id="sidebarMenu">
    <div class="brand">
        <img src="{{ asset('photos/tsvc.png') }}" height="40"><br>
        <small>Takniki Shiksha Careers</small>
    </div>

    <!-- ================= Dashboard ================= -->
    <div class="menu-title">Dashboard</div>
    <a href="{{ route('admin.dashboard') }}"
       class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-gauge me-2"></i> Admin Dashboard
    </a>

    <!-- ================= Analytics ================= -->
    <div class="menu-title">Analytics</div>
    <div>
        <a data-bs-toggle="collapse" href="#analyticsMenu">
            <i class="fas fa-chart-line me-2"></i> Analytics
        </a>
        <div id="analyticsMenu" class="collapse submenu">
            <a href="{{ route('admin.analytics.demos') }}"
               class="{{ request()->routeIs('admin.analytics.demos') ? 'active' : '' }}">
                Demo Conversion Analytics
            </a>

            <a href="{{ route('admin.analytics.followups') }}"
               class="{{ request()->routeIs('admin.analytics.followups') ? 'active' : '' }}">
                Follow-up Analytics
            </a>

            <a href="javascript:void(0)" class="disabled"># Follow-up Trends</a>
            <a href="javascript:void(0)" class="disabled"># Teacher Accountability</a>
        </div>
    </div>

    <!-- ================= Client Management ================= -->
    <div class="menu-title">Client Management</div>
    <div>
        <a data-bs-toggle="collapse" href="#clientMenu">
            <i class="fas fa-users me-2"></i> Client Management
        </a>
        <div id="clientMenu" class="collapse submenu">
            <a href="javascript:void(0)" class="disabled"># All Clients</a>
            <a href="javascript:void(0)" class="disabled"># Client Demo Requests</a>
            <a href="javascript:void(0)" class="disabled"># Client Payments</a>
            <a href="javascript:void(0)" class="disabled"># Change Teacher Requests</a>
        </div>
    </div>

    <!-- ================= Teacher Management ================= -->
    <div class="menu-title">Teacher Management</div>
    <div>
        <a data-bs-toggle="collapse" href="#teacherMenu">
            <i class="fas fa-user-tie me-2"></i> Teacher Management
        </a>
        <div id="teacherMenu" class="collapse submenu">
            <a href="{{ route('admin.teachers.index') }}">All Teachers</a>
            <a href="{{ route('admin.teacher-verifications.index') }}">Teacher Verifications</a>
            <a href="{{ route('admin.demo.followups') }}">Teacher Follow-up Report</a>
            <a class="disabled" href="javascript:void(0)"># Teacher Performance</a>
        </div>
    </div>

    <!-- ================= User Management ================= -->
    <div class="menu-title">User Management</div>
    <div>
        <a data-bs-toggle="collapse" href="#userMenu">
            <i class="fas fa-user-group me-2"></i> User Management
        </a>
        <div id="userMenu" class="collapse submenu">
            <a class="disabled" href="javascript:void(0)"># Students</a>
            <a class="disabled" href="javascript:void(0)"># Volunteers</a>
            <a class="disabled" href="javascript:void(0)"># Interns</a>
            <a class="disabled" href="javascript:void(0)"># Consultants</a>
            <a class="disabled" href="javascript:void(0)"># Partners</a>
            <a class="disabled" href="javascript:void(0)"># Donors</a>
            <a class="disabled" href="javascript:void(0)"># Franchise Centers</a>
        </div>
    </div>

<!-- ================= Workshop Management ================= -->
<div class="menu-title">Workshop Management</div>

<div>
    <a data-bs-toggle="collapse" href="#workshopMenu">
        <i class="fas fa-chalkboard-user me-2"></i> Workshop Management
    </a>

    <div id="workshopMenu" class="collapse submenu">

        {{-- Step 1: Workshop Registrations Table --}}
        <a href="{{ route('admin.workshops.registrations') }}">
            Registrations List
        </a>

        {{-- Step 2: Payment Verification Filter --}}
        <a href="{{ route('admin.workshops.registrations', ['filter' => 'payment_pending']) }}">
            Payments Pending
        </a>

        {{-- Step 3: Attendance Marking Filter --}}
        <a href="{{ route('admin.workshops.registrations', ['filter' => 'attendance_pending']) }}">
            Attendance Marking
        </a>

        {{-- Step 4: Certificate Eligible List --}}
        <a href="{{ route('admin.workshops.registrations', ['filter' => 'certificate_eligible']) }}">
            Certificate Eligible
        </a>

        {{-- Step 5: Already generated certificates --}}
        <a href="{{ route('admin.workshops.registrations', ['filter' => 'certificate_done']) }}">
            Issued Certificates
        </a>
    </div>
</div>


    <!-- ================= Financial Management ================= -->
    <div class="menu-title">Financial Management</div>
    <div>
        <a data-bs-toggle="collapse" href="#financeMenu">
            <i class="fas fa-rupee-sign me-2"></i> Financial Management
        </a>
        <div id="financeMenu" class="collapse submenu">
            <a href="{{ route('admin.payments') }}">Payouts</a>
            <a href="javascript:void(0)" class="disabled"># Finance Dashboard</a>
        </div>
    </div>

    <!-- ================= Settings ================= -->
    <div class="menu-title">Settings & Access Control</div>
    <div>
        <a data-bs-toggle="collapse" href="#settingsMenu">
            <i class="fas fa-gear me-2"></i> Settings & Access Control
        </a>
        <div id="settingsMenu" class="collapse submenu">
            <a href="javascript:void(0)" class="disabled"># Role Permissions</a>
            <a href="javascript:void(0)" class="disabled"># App Settings</a>
            <a href="javascript:void(0)" class="disabled"># Audit Logs</a>
        </div>
    </div>
</div>


<!-- ================= CONTENT WRAPPER (UNCHANGED) ================= -->
<div class="main" id="mainContent">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">@yield('page_title','Admin')</h5>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            
        </form>
    </div>

    @yield('content')
</div>


<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Fixed Sidebar Toggle Logic
const menuBtn = document.getElementById('menuBtn');
const sidebar = document.getElementById('sidebarMenu');
const main = document.getElementById('mainContent');

// Create backdrop for mobile
const backdrop = document.createElement('div');
backdrop.className = 'sidebar-backdrop';
document.body.appendChild(backdrop);

// Mobile sidebar state management
let isMobileSidebarOpen = false;

// Function to close sidebar (mobile)
function closeSidebarMobile() {
    sidebar.classList.remove('show');
    backdrop.classList.remove('show');
    document.body.style.overflow = "";
    menuBtn.innerHTML = '<i class="fas fa-bars"></i>';
    isMobileSidebarOpen = false;
    
    // Remove escape listener
    document.removeEventListener('keydown', handleEscapeKey);
}

// Function to open sidebar (mobile)
function openSidebarMobile() {
    sidebar.classList.add('show');
    backdrop.classList.add('show');
    document.body.style.overflow = "hidden";
    menuBtn.innerHTML = '<i class="fas fa-times"></i>';
    isMobileSidebarOpen = true;
    
    // Add escape listener
    document.addEventListener('keydown', handleEscapeKey);
}

// Main toggle button click
menuBtn.addEventListener('click', function (e) {
    e.stopPropagation();
    
    if (window.innerWidth <= 992) {
        // Mobile behavior
        if (isMobileSidebarOpen) {
            closeSidebarMobile();
        } else {
            openSidebarMobile();
        }
    } else {
        // Desktop behavior
        sidebar.classList.toggle('hide-sidebar');
        main.classList.toggle('full-width');
        
        // Save preference
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('hide-sidebar'));
    }
});

// Close sidebar on backdrop click
backdrop.addEventListener('click', closeSidebarMobile);

// Escape key handler
function handleEscapeKey(event) {
    if (event.key === 'Escape' && window.innerWidth <= 992 && isMobileSidebarOpen) {
        closeSidebarMobile();
    }
}

// FIXED: Handle sidebar link clicks properly
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all collapse menus properly
    const collapseElements = document.querySelectorAll('[data-bs-toggle="collapse"]');
    
    collapseElements.forEach(element => {
        element.addEventListener('click', function(e) {
            if (window.innerWidth <= 992) {
                // On mobile, let Bootstrap handle the collapse
                // Don't prevent default - let it work normally
                
                // Get the target collapse menu
                const targetId = this.getAttribute('href');
                if (targetId) {
                    const target = document.querySelector(targetId);
                    if (target) {
                        // Use Bootstrap's collapse
                        const bsCollapse = bootstrap.Collapse.getOrCreateInstance(target, {
                            toggle: true
                        });
                    }
                }
                
                // DO NOT close sidebar for collapse toggles
                e.stopPropagation();
                return;
            }
        });
    });
    
    // Handle regular navigation links (not collapse toggles)
    document.querySelectorAll('.sidebar a').forEach(link => {
        link.addEventListener('click', function(e) {
            // Check what type of link this is
            const href = this.getAttribute('href');
            const isCollapseToggle = this.hasAttribute('data-bs-toggle') && 
                                    this.getAttribute('data-bs-toggle') === 'collapse';
            const isDisabled = this.classList.contains('disabled');
            const isJavascriptLink = href && href.startsWith('javascript');
            const isAnchorLink = href && href.startsWith('#');
            
            // Only close sidebar for actual page navigation links on mobile
            if (window.innerWidth <= 992 && 
                !isCollapseToggle && 
                !isDisabled && 
                !isJavascriptLink && 
                !isAnchorLink &&
                href && href !== '#') {
                
                // Close sidebar after a small delay for better UX
                setTimeout(closeSidebarMobile, 300);
            }
        });
    });
    
    // Restore sidebar state on load
    if (window.innerWidth > 992) {
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (isCollapsed) {
            sidebar.classList.add('hide-sidebar');
            main.classList.add('full-width');
        }
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992) {
            // Reset mobile states when switching to desktop
            closeSidebarMobile();
        }
    });
    
    // Update menu icon based on state
    const updateMenuIcon = () => {
        if (window.innerWidth <= 992) {
            menuBtn.innerHTML = isMobileSidebarOpen 
                ? '<i class="fas fa-times"></i>' 
                : '<i class="fas fa-bars"></i>';
        } else {
            menuBtn.innerHTML = sidebar.classList.contains('hide-sidebar')
                ? '<i class="fas fa-bars"></i>'
                : '<i class="fas fa-times"></i>';
        }
    };
    
    // Set initial icon
    updateMenuIcon();
});
</script>

@stack('scripts')

</body>
</html>
