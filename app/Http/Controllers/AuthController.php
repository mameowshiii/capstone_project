<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Resident;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectUser();
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt login with username or email
        $loginField = filter_var($credentials['username'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($loginField, $credentials['username'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            if ($user->status === 'inactive') {
                return back()->with('error', 'Your account is pending approval by the administrator.');
            }
            if ($user->status === 'suspended') {
                return back()->with('error', 'Your account has been suspended.');
            }

            Auth::login($user, $request->has('remember'));
            $request->session()->regenerate();

            ActivityLog::log('LOGIN', 'Auth', 'User logged in');

            return $this->redirectUser();
        }

        return back()->with('error', 'Invalid username or password. Please try again.');
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|regex:/^[a-zA-Z\s\-\.,]+$/',
            'last_name' => 'required|string|regex:/^[a-zA-Z\s\-\.,]+$/',
            'middle_name' => 'nullable|string|regex:/^[a-zA-Z\s\-\.,]+$/',
            'gender' => 'required|in:Male,Female,Other',
            'birthdate' => 'required|date|before_or_equal:today',
            'civil_status' => 'required|in:Single,Married,Widowed,Separated',
            'contact_number' => 'nullable|string',
            'purok' => 'nullable|string',
            'years_of_residency' => 'nullable|integer|min:0',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
        ], [
            'first_name.regex' => 'First name must contain only letters, spaces, hyphens, and periods.',
            'last_name.regex' => 'Last name must contain only letters, spaces, hyphens, and periods.',
            'middle_name.regex' => 'Middle name must contain only letters, spaces, hyphens, and periods.',
        ]);

        try {
            DB::beginTransaction();

            // 1. Create Resident profile
            $resident = Resident::create([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'birthdate' => $request->birthdate,
                'civil_status' => $request->civil_status,
                'contact_number' => $request->contact_number,
                'email' => $request->email,
                'address' => 'Barangay Pili, Madridejos, Cebu', // Default fallback address
                'purok' => $request->purok,
                'voter_status' => 'Not Registered',
                'years_of_residency' => $request->years_of_residency ?? 0,
                'status' => 'active'
            ]);

            // 2. Create User account linked to Resident
            User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'resident',
                'status' => 'inactive', // pending approval
                'resident_id' => $resident->id,
            ]);

            DB::commit();

            return redirect()->route('login')->with('success', 'Registration successful! Your account is pending approval by the administrator.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Registration failed. Please try again.')->withInput();
        }
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            ActivityLog::log('LOGOUT', 'Auth', 'User logged out');
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectUser()
    {
        $role = Auth::user()->role;
        if ($role === 'resident') {
            return redirect()->route('resident.my_requests');
        }
        return redirect()->route('admin.dashboard');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'No account found with this email address.',
        ]);

        $token = \Illuminate\Support\Str::random(60);
        $hashedToken = hash('sha256', $token);

        \Illuminate\Support\Facades\DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $hashedToken,
                'created_at' => now()
            ]
        );

        $resetUrl = route('password.reset', ['token' => $token]) . '?email=' . urlencode($request->email);

        $email = $request->email;
        try {
            \Illuminate\Support\Facades\Mail::send('emails.forgot-password', ['resetUrl' => $resetUrl], function ($message) use ($email) {
                $message->to($email);
                $message->subject('Reset Password - Barangay Pili Clearance & Certificate System');
            });

            \Illuminate\Support\Facades\Log::info("Password reset link requested for {$email}: {$resetUrl}");

            if (config('app.env') === 'local' || config('app.debug')) {
                return back()->with('success', 'Password reset link sent! Check laravel.log or use this link to reset: ' . $resetUrl);
            }

            return back()->with('success', 'We have emailed your password reset link!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send password reset email to {$email}: " . $e->getMessage());
            \Illuminate\Support\Facades\Log::info("Password reset link (fallback) for {$email}: {$resetUrl}");

            if (config('app.env') === 'local' || config('app.debug')) {
                return back()->with('success', 'Email sending failed, but link generated in log. Use this link: ' . $resetUrl);
            }

            return back()->with('error', 'Failed to send reset email. Please contact support.');
        }
    }

    public function showResetPassword($token, Request $request)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email')
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $record = \Illuminate\Support\Facades\DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$record || !hash_equals($record->token, hash('sha256', $request->token))) {
            return back()->withErrors(['email' => 'This password reset token is invalid.']);
        }

        $expiresAt = \Carbon\Carbon::parse($record->created_at)->addMinutes(60);
        if ($expiresAt->isPast()) {
            \Illuminate\Support\Facades\DB::table('password_resets')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'This password reset token has expired.']);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        \Illuminate\Support\Facades\DB::table('password_resets')->where('email', $request->email)->delete();

        ActivityLog::log('PASSWORD_RESET', 'Auth', "User {$user->username} reset their password");

        return redirect()->route('login')->with('success', 'Your password has been successfully reset! You can now log in.');
    }
}
