<?php

namespace App\Filament\Resources\InventoryItemResource\Pages;

use App\Filament\Resources\InventoryItemResource;
use App\Models\ProductVariant;
use Filament\Resources\Pages\CreateRecord;

class CreateInventoryItem extends CreateRecord
{
    protected static string $resource = InventoryItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $variant = ProductVariant::with('product')->findOrFail($data['product_variant_id']);

        abort_unless($variant->product->organisation_id === auth()->user()->current_organisation_id, 403);

        return $data;
    }
}
