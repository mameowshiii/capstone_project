<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PhilSmsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhilSmsServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_formats_various_philippine_phone_number_formats_correctly()
    {
        $service = new PhilSmsService();

        // Standard local format (11 digits, starts with 09)
        $this->assertEquals('639171234567', $service->formatPhoneNumber('09171234567'));

        // Local format with spaces/dashes
        $this->assertEquals('639171234567', $service->formatPhoneNumber('0917-123-4567'));
        $this->assertEquals('639171234567', $service->formatPhoneNumber(' 0917 123 4567 '));

        // International format (12 digits, starts with 639)
        $this->assertEquals('639171234567', $service->formatPhoneNumber('639171234567'));
        $this->assertEquals('639171234567', $service->formatPhoneNumber('+639171234567'));
        $this->assertEquals('639171234567', $service->formatPhoneNumber('+63 917 123 4567'));

        // 10-digit format (omits leading 0)
        $this->assertEquals('639171234567', $service->formatPhoneNumber('9171234567'));
        $this->assertEquals('639171234567', $service->formatPhoneNumber(' 917-123-4567 '));

        // Erroneous 13-digit format (6309XXXXXXXXX)
        $this->assertEquals('639171234567', $service->formatPhoneNumber('6309171234567'));
        $this->assertEquals('639171234567', $service->formatPhoneNumber('+6309171234567'));

        // Invalid numbers
        $this->assertNull($service->formatPhoneNumber(''));
        $this->assertNull($service->formatPhoneNumber('abcdef'));
    }

    /** @test */
    public function it_sends_sms_successfully_when_enabled_and_properly_configured()
    {
        Http::fake([
            'https://dashboard.philsms.com/*' => Http::response([
                'status' => 'success',
                'data' => 'sms reports with all details'
            ], 200)
        ]);

        // Configure services for testing
        config([
            'services.philsms.enabled' => true,
            'services.philsms.api_token' => 'test-token-123 ', // trailing space to test trim
            'services.philsms.api_url' => 'https://dashboard.philsms.com/api/v3/sms/send ', // trailing space to test trim
            'services.philsms.sender_id' => 'PhilSMS ', // trailing space to test trim
        ]);

        $service = new PhilSmsService();
        $result = $service->sendSms('09171234567', 'Test message here');

        $this->assertTrue($result);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://dashboard.philsms.com/api/v3/sms/send' &&
                $request->hasHeader('Authorization', 'Bearer test-token-123') &&
                $request->hasHeader('Accept', 'application/json') &&
                $request->hasHeader('Content-Type', 'application/json') &&
                $request['recipient'] === '639171234567' &&
                $request['sender_id'] === 'PhilSMS' &&
                $request['type'] === 'plain' &&
                $request['message'] === 'Test message here';
        });
    }

    /** @test */
    public function it_returns_false_and_skips_dispatch_when_disabled()
    {
        Http::fake();

        config([
            'services.philsms.enabled' => false,
            'services.philsms.api_token' => 'test-token',
        ]);

        $service = new PhilSmsService();
        $result = $service->sendSms('09171234567', 'Test message');

        $this->assertFalse($result);
        Http::assertNothingSent();
    }

    /** @test */
    public function it_returns_false_and_skips_dispatch_when_api_token_is_missing()
    {
        Http::fake();

        config([
            'services.philsms.enabled' => true,
            'services.philsms.api_token' => '',
        ]);

        $service = new PhilSmsService();
        $result = $service->sendSms('09171234567', 'Test message');

        $this->assertFalse($result);
        Http::assertNothingSent();
    }

    /** @test */
    public function it_handles_failed_api_response_gracefully()
    {
        Http::fake([
            'https://dashboard.philsms.com/*' => Http::response([
                'status' => 'error',
                'message' => 'Sender ID is invalid'
            ], 422)
        ]);

        config([
            'services.philsms.enabled' => true,
            'services.philsms.api_token' => 'test-token',
            'services.philsms.api_url' => 'https://dashboard.philsms.com/api/v3/sms/send',
            'services.philsms.sender_id' => 'PhilSMS',
        ]);

        $service = new PhilSmsService();
        $result = $service->sendSms('09171234567', 'Test message');

        $this->assertFalse($result);
    }
}
