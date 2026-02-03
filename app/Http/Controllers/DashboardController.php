<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function adminDashboard(Request $request)
    {
        $query = \App\Models\Mobilnost::with(['student', 'fakultet'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('ime', 'ilike', "%{$search}%")
                  ->orWhere('prezime', 'ilike', "%{$search}%")
                  ->orWhere('br_indexa', 'ilike', "%{$search}%");
            });
        }

        $mobilnosti = $query->paginate(7)->withQueryString();
        return view('dashboard.admin-dashboard', compact('mobilnosti'));
    }

    public function profesorDashboard()
    {
        $user = auth()->user();
        
        $mappingRequests = \App\Models\MappingRequest::whereHas('subjects', function ($query) use ($user) {
                $query->where('professor_id', $user->id);
            })
            ->with(['fakultet', 'subjects.straniPredmet', 'subjects' => function ($query) use ($user) {
            }])
            ->latest()
            ->get();

        return view('dashboard.profesor-dashboard', compact('mappingRequests'));
    }
}
