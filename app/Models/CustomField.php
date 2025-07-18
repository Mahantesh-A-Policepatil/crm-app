<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * CustomField
 *
 * Defines a dynamic, user-defined field that can be associated with contacts.
 * Examples: Date of Birth, Company, Address, etc.
 *
 * @author Mahantesh-A-Policepatil
 * @date 2025-07-16
 */
class CustomField extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'type'];

    /**
     * @return HasMany
     */
    public function values(): HasMany
    {
        return $this->hasMany(ContactCustomFieldValue::class);
    }
}
