<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Resident;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_page_renders_successfully()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Sign In');
        $response->assertSee('Register');
    }

    public function test_user_can_login_with_correct_credentials()
    {
        // Seed a temporary user
        $user = User::create([
            'username' => 'testuser',
            'email' => 'test@user.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'username' => 'testuser',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_incorrect_password()
    {
        $user = User::create([
            'username' => 'testuser',
            'email' => 'test@user.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'username' => 'testuser',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_resident_registration_creates_profile_and_inactive_account()
    {
        $response = $this->post('/register', [
            'first_name' => 'Maria Clara',
            'last_name' => 'Santos',
            'middle_name' => 'Dela Cruz',
            'gender' => 'Female',
            'birthdate' => '2000-01-01',
            'civil_status' => 'Single',
            'contact_number' => '09123456789',
            'purok' => 'Purok 1',
            'years_of_residency' => 5,
            'email' => 'clara@santos.com',
            'username' => 'mariaclara',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        // Check resident profile exists
        $resident = Resident::where('email', 'clara@santos.com')->first();
        $this->assertNotNull($resident);
        $this->assertEquals('Maria Clara', $resident->first_name);

        // Check user account exists and is inactive (pending approval)
        $user = User::where('username', 'mariaclara')->first();
        $this->assertNotNull($user);
        $this->assertEquals('resident', $user->role);
        $this->assertEquals('inactive', $user->status);
        $this->assertEquals($resident->id, $user->resident_id);
    }
}
