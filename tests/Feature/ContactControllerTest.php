<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Feature tests for ContactController.
 *
 * This class contains tests to verify core CRUD operations and
 * additional functionality such as merging contacts in the CRM application.
 *
 * @author Mahantesh-A-Policepatil
 * @date 2025-07-16
 */
class ContactControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected $password = 'secret123';

    protected function loginUser(): User
    {
        $user = User::factory()->create([
            'password' => bcrypt($this->password),
        ]);
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class); // ✅ Skip CSRF
        $this->post('/login', [
            'email' => $user->email,
            'password' => $this->password,
        ])->assertRedirect('/home');

        return $user;
    }

    /** @test */
    public function a_user_can_login_and_view_contacts_index_view()
    {
        $this->loginUser();

        $response = $this->get(route('contacts.index'));

        $response->assertStatus(200);
        $response->assertViewIs('contacts.index');
    }

    /** @test */
    public function a_user_can_login_and_create_a_new_contact()
    {
        $this->loginUser();
        Storage::fake('public');

        $response = $this->postJson(route('contacts.store'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'gender' => 'male',
            'profile_image' => UploadedFile::fake()->image('profile.jpg'),
            'additional_file' => UploadedFile::fake()->create('doc.pdf', 100),
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('contacts', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    /** @test */
    public function a_user_can_login_and_update_an_existing_contact()
    {
        $this->loginUser();
        Storage::fake('public');

        $contact = Contact::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        $response = $this->putJson(route('contacts.update', $contact->id), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '8888888888',
            'gender' => 'male',
            'profile_image' => UploadedFile::fake()->image('updated.jpg'),
            'additional_file' => UploadedFile::fake()->create('updated.pdf', 100),
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '8888888888',
            'gender' => 'male',
        ]);

        $updatedContact = Contact::find($contact->id);
        Storage::disk('public')->assertExists($updatedContact->profile_image);
        Storage::disk('public')->assertExists($updatedContact->additional_file);
    }

    /** @test */
    public function a_user_can_login_and_delete_a_contact()
    {
        $this->loginUser();

        $contact = Contact::factory()->create();

        $response = $this->deleteJson(route('contacts.destroy', $contact->id));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('contacts', [
            'id' => $contact->id,
        ]);
    }

    /** @test */
    public function a_user_can_login_and_merge_contacts()
    {
        $this->loginUser();

        $master = Contact::factory()->create();
        $secondary = Contact::factory()->create();

        $response = $this->postJson(route('contacts.merge'), [
            'master_id' => $master->id,
            'secondary_ids' => [$secondary->id],
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Contacts merged successfully.']);

        $this->assertDatabaseHas('contacts', [
            'id' => $secondary->id,
            'is_merged' => true,
        ]);
    }
}

