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
 * @author Mahantesh Policepatil
 * @date 2025-07-16
 */
class ContactControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        // Create a test user and authenticate
        $this->actingAs(\App\Models\User::factory()->create());
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    /** @test */
    public function it_displays_the_contacts_index_view()
    {
        $user = User::factory()->create(); // Assuming you have a User factory
        $this->actingAs($user); // Authenticate

        $response = $this->get(route('contacts.index'));

        $response->assertStatus(200);
        $response->assertViewIs('contacts.index');
    }

    /** @test */
    public function it_can_create_a_new_contact()
    {
        Storage::fake('public');

        $this->withoutMiddleware(); // ✅ Disable CSRF middleware

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
            'email' => 'john@example.com'
        ]);
    }

    /** @test */
    public function it_can_update_an_existing_contact()
    {
        // Fake the storage disk
        Storage::fake('public');

        // Arrange: Create a contact
        $contact = Contact::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'phone' => '9999999999',
        ]);

        // Act: Send PUT request to update contact
        $response = $this->json('PUT', route('contacts.update', $contact->id), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '8888888888',
            'gender' => 'male',
            'profile_image' => UploadedFile::fake()->image('profile.jpg'),
            'additional_file' => UploadedFile::fake()->create('doc.pdf', 100),
        ]);

        // Assert: Response is OK and JSON success
        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Assert: Updated data exists in DB
        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '8888888888',
            'gender' => 'male',
        ]);

        // Assert: Files were stored
        $updatedContact = Contact::find($contact->id);
        Storage::disk('public')->assertExists($updatedContact->profile_image);
        Storage::disk('public')->assertExists($updatedContact->additional_file);
    }

    /** @test */
    public function it_can_delete_a_contact()
    {
        // Arrange: Create a contact
        $contact = Contact::factory()->create();

        // Act: Send DELETE request
        $response = $this->deleteJson(route('contacts.destroy', $contact->id));

        // Assert: Successful response
        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Assert: Contact was soft deleted
        $this->assertSoftDeleted('contacts', [
            'id' => $contact->id,
        ]);
    }

    /** @test */
    public function it_can_merge_contacts()
    {
        $this->withoutMiddleware();

        $master = Contact::factory()->create();
        $secondary = Contact::factory()->create();

        $response = $this->postJson(route('contacts.merge'), [
            'master_id' => $master->id,
            'secondary_ids' => [$secondary->id]
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Contacts merged successfully.']);

        $this->assertDatabaseHas('contacts', [
            'id' => $secondary->id,
            'is_merged' => true
        ]);
    }
}
