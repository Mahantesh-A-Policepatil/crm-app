<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\CustomField;
use App\Models\ContactCustomFieldValue;
use Faker\Factory as Faker;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Ensure base custom fields exist
        $companyField = CustomField::firstOrCreate(['name' => 'Company'], ['type' => 'text']);
        $birthdayField = CustomField::firstOrCreate(['name' => 'Birthday'], ['type' => 'date']);

        foreach (range(1, 50) as $i) {
            $contact = Contact::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'gender' => $faker->randomElement(['male', 'female', 'other']),
                'profile_image' => null,
                'additional_file' => null,
            ]);

            // Insert custom field values
            ContactCustomFieldValue::create([
                'contact_id' => $contact->id,
                'custom_field_id' => $companyField->id,
                'value' => $faker->company,
            ]);

            ContactCustomFieldValue::create([
                'contact_id' => $contact->id,
                'custom_field_id' => $birthdayField->id,
                'value' => $faker->date('Y-m-d'),
            ]);
        }
    }
}
