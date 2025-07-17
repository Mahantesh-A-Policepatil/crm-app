<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
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
