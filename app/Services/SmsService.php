<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS via PhilSMS API (https://philsms.com).
     *
     * @param  string  $number  Philippine mobile number (09xxxxxxxxx or +639xxxxxxxxx)
     * @param  string  $message
     * @return bool
     */
    public static function send(string $number, string $message): bool
    {
        $apiToken  = config('services.philsms.token');
        $senderId  = config('services.philsms.sender_id', 'PhilSMS');

        if (empty($apiToken)) {
            Log::warning('SmsService: PHILSMS_TOKEN is not configured. SMS not sent.');
            return false;
        }

        // Normalize PH number: remove spaces and leading +
        $normalized = preg_replace('/\s+/', '', $number);
        $normalized = ltrim($normalized, '+');
        // Convert +639xx → 09xx (PhilSMS accepts 09xxxxxxxxx format)
        if (str_starts_with($normalized, '639')) {
            $normalized = '0' . substr($normalized, 2);
        }

        try {
            $client = new \GuzzleHttp\Client(['timeout' => 15]);

            $response = $client->post('https://dashboard.philsms.com/api/v3/sms/send', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiToken,
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'recipient' => $normalized,
                    'sender_id' => $senderId,
                    'type'      => 'plain',
                    'message'   => $message,
                ],
            ]);

            $body = (string) $response->getBody();
            Log::info("SmsService: SMS sent to {$normalized}. Response: {$body}");
            return true;

        } catch (\Exception $e) {
            Log::error("SmsService: Failed to send SMS to {$normalized}. Error: " . $e->getMessage());
            return false;
        }
    }
}
