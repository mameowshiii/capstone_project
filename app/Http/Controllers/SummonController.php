<?php

namespace App\Http\Controllers;

use App\Models\Summon;
use App\Models\Resident;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SummonController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status', 'all');

        $query = Summon::with(['complainantResident', 'respondentResident']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('case_number', 'like', "%{$search}%")
                    ->orWhere('complainant_name', 'like', "%{$search}%")
                    ->orWhere('respondent_name', 'like', "%{$search}%");
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $summons = $query->orderBy('schedule_date', 'asc')->paginate(10);
        $residents = Resident::where('status', 'active')->orderBy('last_name')->get();

        return view('admin.summons', compact('summons', 'residents', 'search', 'status'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'complainant_name' => 'required|string|max:255',
            'complainant_contact' => 'nullable|string|max:100',
            'complainant_resident_id' => 'nullable|exists:residents,id',
            'respondent_name' => 'required|string|max:255',
            'respondent_contact' => 'nullable|string|max:100',
            'respondent_resident_id' => 'nullable|exists:residents,id',
            'complain_details' => 'required|string',
            'schedule_date' => 'required|date|after_or_equal:today',
        ]);

        $caseNumber = 'SUMMON-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        Summon::create([
            'case_number' => $caseNumber,
            'complainant_name' => $request->complainant_name,
            'complainant_contact' => $request->complainant_contact,
            'complainant_resident_id' => $request->complainant_resident_id,
            'respondent_name' => $request->respondent_name,
            'respondent_contact' => $request->respondent_contact,
            'respondent_resident_id' => $request->respondent_resident_id,
            'complain_details' => $request->complain_details,
            'schedule_date' => $request->schedule_date,
            'status' => 'pending',
        ]);

        ActivityLog::log('CREATE_SUMMON', 'Summons', "Created summon case {$caseNumber}");

        return back()->with('success', "Summon case {$caseNumber} created successfully.");
    }

    public function update(Request $request)
    {
        $request->validate([
            'summon_id' => 'required|exists:summons,id',
            'schedule_date' => 'required|date',
            'status' => 'required|in:pending,scheduled,resolved,cancelled',
            'hearing_remarks' => 'nullable|string',
        ]);

        $summon = Summon::findOrFail($request->summon_id);
        $summon->update([
            'schedule_date' => $request->schedule_date,
            'status' => $request->status,
            'hearing_remarks' => $request->hearing_remarks,
        ]);

        ActivityLog::log('UPDATE_SUMMON', 'Summons', "Updated summon case {$summon->case_number}");

        return back()->with('success', "Summon case {$summon->case_number} updated successfully.");
    }

    public function delete($id)
    {
        $summon = Summon::findOrFail($id);
        $caseNum = $summon->case_number;
        $summon->delete();

        ActivityLog::log('DELETE_SUMMON', 'Summons', "Deleted summon case {$caseNum}");

        return back()->with('success', "Summon case {$caseNum} deleted successfully.");
    }

    public function residentIndex()
    {
        $resident = Auth::user()->resident;
        if (!$resident) {
            return redirect()->route('login')->with('error', 'Profile not found.');
        }

        $summons = Summon::with(['complainantResident', 'respondentResident'])
            ->where(function ($q) use ($resident) {
                $q->where('complainant_resident_id', $resident->id)
                    ->orWhere('respondent_resident_id', $resident->id);
            })
            ->orderBy('schedule_date', 'asc')
            ->paginate(10);

        return view('resident.summons', compact('summons'));
    }
}
