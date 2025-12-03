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
        $user = auth()->user();
        
        $predmetiIds = $user->predmeti->pluck('id');

        $agreements = \App\Models\PrepisAgreement::whereIn('fit_predmet_id', $predmetiIds)
            ->with(['prepis.student', 'fitPredmet', 'straniPredmet'])
            ->latest()
            ->get();

        return view('dashboard.profesor-dashboard', compact('agreements'));
    }
}
