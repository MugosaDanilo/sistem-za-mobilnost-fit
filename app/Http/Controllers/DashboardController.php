<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $mobilnosti = \App\Models\Mobilnost::with(['student', 'fakultet'])->latest()->get();
        return view('dashboard.admin-dashboard', compact('mobilnosti'));
    }

    public function profesorDashboard()
    {
        return view('dashboard.profesor-dashboard');
    }
}
