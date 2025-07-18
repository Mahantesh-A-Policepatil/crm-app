<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ContactCustomFieldValue
 *
 * Stores values for custom fields attached to a contact.
 * Acts as a pivot-like table between Contact and CustomField.
 *
 * @author Mahantesh-A-Policepatil
 * @date 2025-07-16
 */
class ContactCustomFieldValue extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['contact_id', 'custom_field_id', 'value'];

    /**
     * @return BelongsTo
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * @return BelongsTo
     */
    public function customField(): BelongsTo
    {
        return $this->belongsTo(CustomField::class);
    }
}
