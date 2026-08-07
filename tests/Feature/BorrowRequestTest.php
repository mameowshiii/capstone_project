<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Resident;
use App\Models\BorrowRequest;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class BorrowRequestTest extends TestCase
{
    use DatabaseTransactions;

    protected $residentUser;
    protected $residentProfile;
    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create resident
        $this->residentProfile = Resident::create([
            'first_name' => 'Maria Clara',
            'last_name' => 'Santos',
            'gender' => 'Female',
            'birthdate' => '2000-01-01',
            'civil_status' => 'Single',
            'email' => 'clara@santos.com',
            'address' => 'Barangay Pili, Madridejos, Cebu',
            'status' => 'active'
        ]);

        $this->residentUser = User::create([
            'username' => 'mariaclara',
            'email' => 'clara@santos.com',
            'password' => bcrypt('password123'),
            'role' => 'resident',
            'status' => 'active',
            'resident_id' => $this->residentProfile->id,
        ]);

        // Create admin
        $this->adminUser = User::create([
            'username' => 'admin_test',
            'email' => 'admin_test@email.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    public function test_resident_can_view_borrows_page()
    {
        $response = $this->actingAs($this->residentUser)->get('/resident/borrows');
        $response->assertStatus(200);
        $response->assertSee('My Borrow Requests');
    }

    public function test_resident_can_submit_valid_borrow_request()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->residentUser)->post('/resident/borrows/store', [
            'item_type' => 'all',
            'tent_quantity' => 5,
            'chair_quantity' => 50,
            'table_quantity' => 25,
            'borrow_date' => now()->format('Y-m-d'),
            'return_date' => now()->addDays(2)->format('Y-m-d'),
            'purpose' => 'Community Gathering',
            'verification_document' => $file,
        ]);

        $response->assertRedirect(route('resident.borrows'));
        $response->assertSessionHas('success');

        $borrow = BorrowRequest::where('resident_id', $this->residentProfile->id)->first();
        $this->assertNotNull($borrow);
        $this->assertEquals(5, $borrow->tent_quantity);
        $this->assertEquals(50, $borrow->chair_quantity);
        $this->assertEquals(25, $borrow->table_quantity);
    }

    public function test_resident_cannot_borrow_more_than_limits()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        // Exceeding tent limit
        $response = $this->actingAs($this->residentUser)->post('/resident/borrows/store', [
            'item_type' => 'all',
            'tent_quantity' => 6, // max is 5
            'chair_quantity' => 10,
            'table_quantity' => 10,
            'borrow_date' => now()->format('Y-m-d'),
            'return_date' => now()->addDays(2)->format('Y-m-d'),
            'purpose' => 'Community Gathering',
            'verification_document' => $file,
        ]);

        $response->assertSessionHasErrors(['tent_quantity']);

        // Exceeding chair limit
        $response = $this->actingAs($this->residentUser)->post('/resident/borrows/store', [
            'item_type' => 'all',
            'tent_quantity' => 2,
            'chair_quantity' => 51, // max is 50
            'table_quantity' => 10,
            'borrow_date' => now()->format('Y-m-d'),
            'return_date' => now()->addDays(2)->format('Y-m-d'),
            'purpose' => 'Community Gathering',
            'verification_document' => $file,
        ]);

        $response->assertSessionHasErrors(['chair_quantity']);

        // Exceeding table limit
        $response = $this->actingAs($this->residentUser)->post('/resident/borrows/store', [
            'item_type' => 'all',
            'tent_quantity' => 2,
            'chair_quantity' => 10,
            'table_quantity' => 26, // max is 25
            'borrow_date' => now()->format('Y-m-d'),
            'return_date' => now()->addDays(2)->format('Y-m-d'),
            'purpose' => 'Community Gathering',
            'verification_document' => $file,
        ]);

        $response->assertSessionHasErrors(['table_quantity']);
    }

    public function test_resident_cannot_submit_all_zero_quantities()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->residentUser)->post('/resident/borrows/store', [
            'item_type' => 'all',
            'tent_quantity' => 0,
            'chair_quantity' => 0,
            'table_quantity' => 0,
            'borrow_date' => now()->format('Y-m-d'),
            'return_date' => now()->addDays(2)->format('Y-m-d'),
            'purpose' => 'Community Gathering',
            'verification_document' => $file,
        ]);

        $response->assertSessionHasErrors(['item_type']);
    }
}
