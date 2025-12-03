<?php

namespace App\Http\Controllers;

use App\Models\PrepisAgreement;
use Illuminate\Http\Request;

class PrepisAgreementController extends Controller
{
    public function accept($id)
    {
        $agreement = PrepisAgreement::findOrFail($id);
        $agreement->update(['status' => 'odobren']);

        return redirect()->back()->with('success', 'Agreement accepted successfully.');
    }

    public function reject($id)
    {
        $agreement = PrepisAgreement::findOrFail($id);
        $agreement->update(['status' => 'odbijen']);

        return redirect()->back()->with('success', 'Agreement rejected successfully.');
    }
}
