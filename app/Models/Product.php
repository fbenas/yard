<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'organisation_id',
        'name',
        'description',
        'variant_dimensions',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'variant_dimensions' => 'array',
        ];
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function disableVariants(string $reason = 'blueprint_changed'): void
    {
        $this->variants()->update([
            'status' => 'disabled',
            'disabled_reason' => $reason,
        ]);
    }
}
