<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhilSmsService
{
    /**
     * Send an SMS using the PhilSMS API v3.
     *
     * @param string $recipientNumber
     * @param string $message
     * @return bool
     */
    public function sendSms($recipientNumber, $message)
    {
        $enabled   = config('services.philsms.enabled', true);
        $apiToken  = config('services.philsms.api_token');
        $apiUrl    = config('services.philsms.api_url', 'https://app.philsms.com/api/v3/sms/send');
        $senderId  = config('services.philsms.sender_id', 'PhilSMS');

        if (!$enabled) {
            Log::info("PhilSMS dispatch skipped (SMS service disabled in config). Recipient: {$recipientNumber}");
            return false;
        }

        if (empty($apiToken)) {
            Log::warning("PhilSMS API Token is missing in environment/config. Unable to send SMS to {$recipientNumber}");
            return false;
        }

        $formattedNumber = $this->formatPhoneNumber($recipientNumber);
        if (empty($formattedNumber)) {
            Log::warning("Invalid phone number provided for PhilSMS: '{$recipientNumber}'");
            return false;
        }

        try {
            $payload = [
                'sender_id' => $senderId,
                'recipient' => $formattedNumber,
                'message'   => $message,
            ];

            $response = Http::withToken($apiToken)
                ->timeout(10)
                ->post($apiUrl, $payload);

            if ($response->successful()) {
                Log::info("PhilSMS sent successfully to {$formattedNumber}: Response: " . $response->body());
                return true;
            } else {
                $status = $response->status();
                $body = $response->body();
                Log::error(
                    "PhilSMS API failed for recipient {$formattedNumber}. URL: {$apiUrl}. HTTP Status: {$status}. Request: " . json_encode($payload) . 
                    ", Response: " . $body
                );

                return false;
            }
        } catch (\Exception $e) {
            $err = $e->getMessage();
            Log::error("Exception occurred while sending PhilSMS to {$formattedNumber}: {$err}. URL: {$apiUrl}. Payload: " . (isset($payload) ? json_encode($payload) : '[]'));
            return false;
        }
    }

    /**
     * Format Philippine contact number into standard 11-digit format (09XXXXXXXXX).
     *
     * @param string $phone
     * @return string|null
     */
    public function formatPhoneNumber($phone)
    {
        if (empty($phone)) {
            return null;
        }

        // Keep numbers only
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        if (empty($cleaned)) {
            return null;
        }

        // Standard 11-digit local format: 09XXXXXXXXX
        if (strlen($cleaned) === 11 && str_starts_with($cleaned, '09')) {
            return $cleaned;
        }

        // International format starting with 639XXXXXXXXX (12 digits) -> convert to 09XXXXXXXXX
        if (strlen($cleaned) === 12 && str_starts_with($cleaned, '639')) {
            return '0' . substr($cleaned, 2);
        }

        // 10-digit number without leading 0: 9XXXXXXXXX
        if (strlen($cleaned) === 10 && str_starts_with($cleaned, '9')) {
            return '0' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Send Equipment / Tent Borrowing Request Status SMS notification.
     *
     * @param \App\Models\BorrowRequest $borrow
     * @return bool
     */
    public function sendBorrowStatusSms($borrow)
    {
        $resident = $borrow->resident;
        if (!$resident || empty($resident->contact_number)) {
            return false;
        }

        $residentName = $resident->first_name;
        $statusUpper  = strtoupper($borrow->status);
        $itemSummary  = $this->buildItemSummary($borrow);
        $dateStr      = $borrow->borrow_date ? date('M d, Y', strtotime($borrow->borrow_date)) : '';
        $remarks      = !empty($borrow->remarks) ? " Remarks: " . $borrow->remarks : "";

        $message = "Barangay Pili Notice: Hello {$residentName}, your equipment borrow request ({$itemSummary}) for {$dateStr} has been marked as {$statusUpper}.{$remarks}";

        return $this->sendSms($resident->contact_number, $message);
    }

    /**
     * Send Summon / Blotter Notice SMS notification.
     *
     * @param \App\Models\Summon $summon
     * @param string $recipientType 'complainant' or 'respondent'
     * @return bool
     */
    public function sendSummonNoticeSms($summon, $recipientType = 'complainant')
    {
        $contactNumber = null;
        $name          = null;

        if ($recipientType === 'complainant') {
            $name          = $summon->complainant_name;
            $contactNumber = $summon->complainant_contact ?? ($summon->complainantResident ? $summon->complainantResident->contact_number : null);
        } else {
            $name          = $summon->respondent_name;
            $contactNumber = $summon->respondent_contact ?? ($summon->respondentResident ? $summon->respondentResident->contact_number : null);
        }

        if (empty($contactNumber)) {
            return false;
        }

        $caseTypeUpper = strtoupper($summon->case_type);
        $statusUpper   = strtoupper($summon->status);
        $scheduleStr   = $summon->schedule_date ? date('M d, Y h:i A', strtotime($summon->schedule_date)) : 'N/A';
        $remarks       = !empty($summon->hearing_remarks) ? " Remarks: " . $summon->hearing_remarks : "";

        $message = "Barangay Pili Notice: Hello {$name}, regarding {$caseTypeUpper} Case #{$summon->case_number}. Status: {$statusUpper}. Hearing Schedule: {$scheduleStr}.{$remarks}";

        return $this->sendSms($contactNumber, $message);
    }

    /**
     * Send Document / Certificate Request Status SMS notification.
     *
     * @param \App\Models\Request $certReq
     * @return bool
     */
    public function sendDocumentStatusSms($certReq)
    {
        $resident = $certReq->resident;
        if (!$resident || empty($resident->contact_number)) {
            return false;
        }

        $certName     = $certReq->certificate ? $certReq->certificate->name : 'Document Request';
        $residentName = $resident->first_name;
        $statusUpper  = strtoupper($certReq->status);
        $trackingNo   = $certReq->tracking_number;
        $remarks      = !empty($certReq->remarks) ? " Remarks: " . $certReq->remarks : "";

        $message = "Barangay Pili Notice: Hello {$residentName}, your request for {$certName} (Tracking #{$trackingNo}) is now {$statusUpper}.{$remarks}";

        return $this->sendSms($resident->contact_number, $message);
    }

    /**
     * Helper to summarize borrowed items for SMS text.
     */
    private function buildItemSummary($borrow)
    {
        $items = [];
        if ($borrow->tent_quantity > 0) {
            $items[] = "{$borrow->tent_quantity} Tent(s)";
        }
        if ($borrow->chair_quantity > 0) {
            $items[] = "{$borrow->chair_quantity} Chair(s)";
        }
        if ($borrow->table_quantity > 0) {
            $items[] = "{$borrow->table_quantity} Table(s)";
        }

        return !empty($items) ? implode(', ', $items) : 'Equipment';
    }
}
