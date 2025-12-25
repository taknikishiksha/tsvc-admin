<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('superadmin.settings.index'); // Ya koi valid view
    }
}
