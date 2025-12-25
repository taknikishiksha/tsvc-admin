<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Application;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_applications' => Application::count(),
            'pending_payments' => Application::where('status', 'pending')->count(),
            'total_revenue' => 0,  // ← FIXED!
            'active_teachers' => User::where('role', 'teacher')->count()
        ];

        return view('superadmin.dashboard.index', compact('stats'));
    }

    public function users()
    {
        $users = User::withTrashed()->paginate(20);
        return view('superadmin.users.index', compact('users'));
    }

    public function courses()
    {
        return view('superadmin.courses.index');
    }

    public function jobs()
    {
        return view('superadmin.jobs.index');
    }

    public function services()
    {
        return view('superadmin.services.index');
    }

    public function finance()
    {
        return view('superadmin.finance.index');
    }

    public function settings()
    {
        return view('superadmin.settings.index');
    }

    public function logs()
    {
        return view('superadmin.logs.index');
    }
}