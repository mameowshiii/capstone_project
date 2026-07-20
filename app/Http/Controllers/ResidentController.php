<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use App\Models\Certificate;
use App\Models\Request as CertificateRequest;
use App\Models\Payment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ResidentController extends Controller
{
    public function myRequests()
    {
        $resident = Auth::user()->resident;
        if (!$resident) {
            return redirect()->route('login')->with('error', 'Profile not found.');
        }

        $requests = CertificateRequest::with(['certificate', 'payment'])
            ->where('resident_id', $resident->id)
            ->whereNull('archived_at')
            ->orderBy('requested_at', 'desc')
            ->paginate(10);

        return view('resident.my_requests', compact('requests'));
    }

    public function newRequestForm()
    {
        $resident = Auth::user()->resident;
        $certs = Certificate::where('status', 'active')->orderBy('name')->get();
        return view('resident.request', compact('resident', 'certs'));
    }

    public function submitRequest(Request $request)
    {
        $request->validate([
            'certificate_id' => 'required|exists:certificates,id',
            'purpose' => 'required|string|max:500',
        ]);

        $resident = Auth::user()->resident;
        if (!$resident) {
            return redirect()->route('login')->with('error', 'Profile not found.');
        }

        $cert = Certificate::findOrFail($request->certificate_id);

        // Generate unique tracking number
        $trackingNumber = 'PILI-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

        // Create Request
        $certReq = CertificateRequest::create([
            'tracking_number' => $trackingNumber,
            'resident_id' => $resident->id,
            'certificate_id' => $cert->id,
            'purpose' => $request->purpose,
            'status' => 'pending',
        ]);

        // Create Payment
        Payment::create([
            'request_id' => $certReq->id,
            'amount' => $cert->fee,
            'payment_method' => 'cash',
            'payment_status' => ($cert->fee == 0) ? 'waived' : 'unpaid',
        ]);

        ActivityLog::log('NEW_REQUEST', 'Requests', "Filed request $trackingNumber");

        return redirect()->route('resident.my_requests')
            ->with('success', "Request submitted! Tracking number: $trackingNumber. Please proceed to Barangay Pili Office for payment.");
    }

    public function profileForm()
    {
        $resident = Auth::user()->resident;
        return view('resident.profile', compact('resident'));
    }

    public function updateProfile(Request $request)
    {
        $resident = Auth::user()->resident;
        if (!$resident) {
            return redirect()->route('login')->with('error', 'Profile not found.');
        }

        $action = $request->input('action');

        if ($action === 'update_profile') {
            $request->validate([
                'first_name' => 'required|string|regex:/^[a-zA-Z\s\-\.,]+$/',
                'last_name' => 'required|string|regex:/^[a-zA-Z\s\-\.,]+$/',
                'middle_name' => 'nullable|string|regex:/^[a-zA-Z\s\-\.,]+$/',
                'contact_number' => 'nullable|string|max:20',
                'address' => 'required|string|max:255',
                'purok' => 'nullable|string|max:100',
                'occupation' => 'nullable|string|max:150',
                'civil_status' => 'required|in:Single,Married,Widowed,Separated',
                'voter_status' => 'required|in:Registered,Not Registered',
            ], [
                'first_name.regex' => 'First name must contain only letters, spaces, hyphens, and periods.',
                'last_name.regex' => 'Last name must contain only letters, spaces, hyphens, and periods.',
                'middle_name.regex' => 'Middle name must contain only letters, spaces, hyphens, and periods.',
            ]);

            $resident->update($request->only([
                'first_name', 'middle_name', 'last_name', 'contact_number',
                'address', 'purok', 'occupation', 'civil_status', 'voter_status'
            ]));

            ActivityLog::log('UPDATE_PROFILE', 'Profile', 'Resident updated profile info');
            return back()->with('success', 'Profile updated successfully.');

        } elseif ($action === 'change_password') {
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6',
                'confirm_password' => 'required|string|same:new_password',
            ]);

            $user = Auth::user();

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Current password is incorrect.');
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            ActivityLog::log('CHANGE_PASSWORD', 'Profile', 'Password changed');
            return back()->with('success', 'Password changed successfully.');

        } elseif ($action === 'update_photo') {
            $request->validate([
                'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/uploads/profiles'), $filename);

                // Delete old photo
                if ($resident->photo && file_exists(public_path('assets/uploads/' . $resident->photo))) {
                    @unlink(public_path('assets/uploads/' . $resident->photo));
                }

                $resident->update([
                    'photo' => 'profiles/' . $filename
                ]);

                ActivityLog::log('UPDATE_PROFILE_PHOTO', 'Profile', 'Resident updated profile photo');
                return back()->with('success', 'Profile photo updated.');
            }

            return back()->with('error', 'Unable to upload photo.');

        } elseif ($action === 'remove_photo') {
            if ($resident->photo && file_exists(public_path('assets/uploads/' . $resident->photo))) {
                @unlink(public_path('assets/uploads/' . $resident->photo));
            }

            $resident->update([
                'photo' => null
            ]);

            ActivityLog::log('REMOVE_PROFILE_PHOTO', 'Profile', 'Resident removed profile photo');
            return back()->with('success', 'Profile photo removed.');
        }

        return back()->with('error', 'Invalid action.');
    }
}
