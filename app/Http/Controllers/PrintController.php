<?php

namespace App\Http\Controllers;

use App\Models\Request as CertificateRequest;
use App\Models\Official;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrintController extends Controller
{
    public function print($id, Request $request)
    {
        $certReq = CertificateRequest::with(['resident', 'certificate', 'payment', 'approver'])
            ->findOrFail($id);

        // Security check: residents can only print their own approved/released requests
        if (Auth::user()->role === 'resident') {
            if (Auth::user()->resident_id !== $certReq->resident_id) {
                abort(403, 'Unauthorized.');
            }
            if (!in_array($certReq->status, ['approved', 'released'])) {
                abort(403, 'Document is not ready for printing.');
            }
        }

        // Fetch active officials for sidebar list
        $officials = Official::where('status', 'active')
            ->orderBy('sort_order')
            ->get();

        // Find Punong Barangay
        $captain = Official::where('status', 'active')
            ->where(function ($q) {
                $q->where('position', 'like', '%Captain%')
                  ->orWhere('position', 'like', '%Punong%');
            })->first();
        
        $captainName = $captain ? $captain->name : 'HON. JERRY S. CARANZO';

        // Auto print check
        $autoPrint = $request->has('print');

        // Determine view from template_file column
        $tpl = $certReq->certificate->template_file ?? 'certificate.php';
        $viewName = 'print.general';

        if (str_contains($tpl, 'indigency')) {
            $viewName = 'print.indigency';
        } elseif (str_contains($tpl, 'residency')) {
            $viewName = 'print.residency';
        } elseif (str_contains($tpl, 'moral')) {
            $viewName = 'print.moral';
        } elseif (str_contains($tpl, 'clearance')) {
            $viewName = 'print.clearance';
        }

        return view($viewName, compact('certReq', 'officials', 'captainName', 'autoPrint'));
    }
}
