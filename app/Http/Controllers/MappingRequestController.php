<?php

namespace App\Http\Controllers;

use App\Models\MappingRequest;
use App\Models\MappingRequestSubject;
use App\Models\Predmet;
use Illuminate\Http\Request;

class MappingRequestController extends Controller
{
    public function show($id)
    {
        $mappingRequest = MappingRequest::with(['fakultet', 'subjects.straniPredmet', 'subjects.fitPredmet'])->findOrFail($id);
        
        // Ensure the logged-in professor owns this request
        if ($mappingRequest->professor_id !== auth()->id()) {
            abort(403);
        }

        // Professor's subjects (FIT subjects)
        // Assuming professor is linked to subjects via 'profesor_predmet' table or similar relationship
        // Based on DashboardController, user->predmeti gives the subjects
        $professorSubjects = auth()->user()->predmeti;

        return view('mapping_request.show', compact('mappingRequest', 'professorSubjects'));
    }

    public function update(Request $request, $id)
    {
        $mappingRequest = MappingRequest::findOrFail($id);

        if ($mappingRequest->professor_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'mappings' => 'array',
            'mappings.*.request_subject_id' => 'required|exists:mapping_request_subjects,id',
            'mappings.*.fit_predmet_id' => 'required|exists:predmeti,id',
        ]);

        // Reset all mappings for this request first? Or just update provided ones?
        // User said "After professor finishes linking... click button to save connections"
        // And "Status changes from Pending to Completed"

        // Get all subjects for this request
        $allRequestSubjects = MappingRequestSubject::where('mapping_request_id', $mappingRequest->id)->get();
        
        // Map input mappings by ID for easy lookup
        $inputMappings = collect($request->input('mappings', []))->keyBy('request_subject_id');

        foreach ($allRequestSubjects as $subject) {
            if ($inputMappings->has($subject->id)) {
                // Update with new fit_predmet_id
                $subject->update([
                    'fit_predmet_id' => $inputMappings->get($subject->id)['fit_predmet_id']
                ]);
            } else {
                // Clear mapping if not present in input
                $subject->update([
                    'fit_predmet_id' => null
                ]);
            }
        }

        $mappingRequest->update(['status' => 'completed']);

        session()->flash('success', 'Mappings saved successfully.');

        return response()->json(['message' => 'Mappings saved successfully.']);
    }
}
