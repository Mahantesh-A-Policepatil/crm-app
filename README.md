
# CRM Project

A simple Customer Relationship Management (CRM) system built with Laravel. This project manages contacts, custom fields, and supports merging and soft-deletion of contact records.

## 📦 Requirements

- PHP >= 8.1
- Laravel 9.x
- MySQL or compatible database
- Node.js & npm (for front-end assets, if applicable)

## 🚀 Setup Instructions

1. Clone the repository:

   ```bash
   git clone https://github.com/your-username/crm-app.git
   cd crm-app
   ```

2. Install dependencies:

   ```bash
   composer install
   npm install && npm run dev
   ```

3. Configure `.env`:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Run migrations and seed data:

   ```bash
   php artisan migrate
   php artisan db:seed --class=ContactSeeder
   ```

5. Serve the application:

   ```bash
   php artisan serve
   ```

## ⚙️ Artisan Commands Used in This Project

### Migrations

- Create `contacts` table  
  ```bash
  php artisan make:migration create_contacts_table
  ```

- Create `custom_fields` table  
  ```bash
  php artisan make:migration create_custom_fields_table
  ```

- Create `contact_custom_field_values` table  
  ```bash
  php artisan make:migration create_contact_custom_field_values_table
  ```

### Models

- Contact Model  
  ```bash
  php artisan make:model Contact
  ```

- ContactCustomFieldValue Model  
  ```bash
  php artisan make:model ContactCustomFieldValue
  ```

- CustomField Model  
  ```bash
  php artisan make:model CustomField
  ```

### Controllers

- ContactController  
  ```bash
  php artisan make:controller ContactController
  ```

### Seeding

- Seed the `contacts` table  
  ```bash
  php artisan db:seed --class=ContactSeeder
  ```

## 🧪 Testing

Run PHPUnit test cases:

```bash
php artisan test
```

## 📁 Folder Structure

- `app/Models` - Eloquent models (Contact, CustomField, etc.)
- `app/Http/Controllers` - Controller logic
- `resources/views` - Blade templates
- `routes/web.php` - Route definitions
- `tests/Feature` - Feature test cases

## ✅ Features

- CRUD for contacts
- Upload profile images & documents
- Custom field support per contact
- Merge duplicate contacts
- Soft delete with restore possibility
- AJAX-powered UI using Yajra DataTables

## 🧑‍💻 Author

**Mahantesh A Policepatil**  
GitHub: [Mahantesh-A-Policepatil](https://github.com/Mahantesh-A-Policepatil)

---

Feel free to contribute, raise issues, or suggest improvements!
