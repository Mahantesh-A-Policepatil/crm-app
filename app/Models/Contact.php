<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Contact
 *
 * Represents a contact in the CRM.
 * Stores personal details and is associated with custom fields and merge status.
 *
 * @author Mahantesh-A-Policepatil
 * @date 2025-07-16
 */
class Contact extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name', 'email', 'phone', 'gender', 'profile_image', 'additional_file', 'is_merged', 'merged_into'
    ];

    /**
     * @return HasMany
     */
    public function customFieldValues(): HasMany
    {
        return $this->hasMany(ContactCustomFieldValue::class);
    }

    /**
     * @return BelongsTo
     */
    public function mergedContact()
    {
        return $this->belongsTo(Contact::class, 'merged_into');
    }

    /**
     * @return HasMany
     */
    public function mergedContacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'merged_into');
    }
}
