<?php

namespace App\Http\Controllers;

use App\Models\Request as CertificateRequest;
use App\Models\Payment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status', 'all');
        $viewTracking = $request->input('view');

        $query = CertificateRequest::with(['resident', 'certificate', 'payment', 'processor', 'approver'])
            ->whereNull('archived_at');

        // Status Filter
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Search Filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%")
                  ->orWhereHas('resident', function ($qr) use ($search) {
                      $qr->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $requests = $query->orderBy('requested_at', 'desc')->paginate(12);

        $viewedRequest = null;
        if ($viewTracking) {
            $viewedRequest = CertificateRequest::with(['resident', 'certificate', 'payment', 'processor', 'approver'])
                ->where('tracking_number', $viewTracking)
                ->first();
        }

        return view('admin.requests', compact('requests', 'viewedRequest', 'status', 'search'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:requests,id',
            'action' => 'required|in:process,reject,approve,release,archive',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $certReq = CertificateRequest::findOrFail($request->request_id);
        $action = $request->action;
        $userId = Auth::id();
        $msg = '';

        if ($action === 'process') {
            $certReq->update([
                'status' => 'processing',
                'processed_by' => $userId,
                'processed_at' => now(),
            ]);
            $msg = 'Request marked as processing.';
        } elseif ($action === 'reject') {
            $certReq->update([
                'status' => 'rejected',
                'remarks' => $request->remarks,
                'processed_by' => $userId,
                'processed_at' => now(),
            ]);
            $msg = 'Request rejected.';
        } elseif ($action === 'approve') {
            $certReq->update([
                'status' => 'approved',
                'approved_by' => $userId,
                'approved_at' => now(),
                'remarks' => $request->remarks,
            ]);
            if (!$certReq->processed_by) {
                $certReq->update([
                    'processed_by' => $userId,
                    'processed_at' => now(),
                ]);
            }
            if ($certReq->payment && $certReq->payment->payment_method === 'gcash') {
                $certReq->payment->update([
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                    'received_by' => $userId,
                ]);
            }
            $msg = 'Request approved successfully.';
        } elseif ($action === 'release') {
            $certReq->update([
                'status' => 'released',
                'released_at' => now(),
            ]);
            if ($certReq->payment) {
                $certReq->payment->update([
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                    'received_by' => $userId,
                ]);
            }
            $msg = 'Request released and marked as paid.';
        } elseif ($action === 'archive') {
            $certReq->update([
                'archived_at' => now(),
                'archived_by' => $userId,
            ]);
            $msg = 'Request archived safely. You can restore it from Archive.';
        }

        ActivityLog::log(
            strtoupper($action) . '_REQUEST', 
            'Requests', 
            "Performed action '{$action}' on request {$certReq->tracking_number}"
        );

        return redirect()->route('admin.requests')->with('success', $msg);
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:requests,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,gcash,maya',
            'payment_status' => 'required|in:paid,unpaid,waived',
            'receipt_number' => 'nullable|string|max:100',
        ]);

        $certReq = CertificateRequest::findOrFail($request->request_id);

        if (!$certReq->payment) {
            Payment::create([
                'request_id' => $certReq->id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'receipt_number' => $request->receipt_number,
                'paid_at' => ($request->payment_status === 'paid') ? now() : null,
                'received_by' => Auth::id(),
            ]);
        } else {
            $certReq->payment->update([
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'receipt_number' => $request->receipt_number,
                'paid_at' => ($request->payment_status === 'paid') ? now() : null,
                'received_by' => Auth::id(),
            ]);
        }

        ActivityLog::log(
            'PROCESS_PAYMENT', 
            'Payments', 
            "Processed payment for request {$certReq->tracking_number}. Status: {$request->payment_status}"
        );

        return back()->with('success', 'Payment details updated successfully.');
    }
}
