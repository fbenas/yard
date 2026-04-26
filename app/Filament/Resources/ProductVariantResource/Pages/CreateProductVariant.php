<?php

namespace App\Filament\Resources\ProductVariantResource\Pages;

use App\Filament\Resources\ProductVariantResource;
use App\Models\Product;
use Filament\Resources\Pages\CreateRecord;

class CreateProductVariant extends CreateRecord
{
    protected static string $resource = ProductVariantResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $product = Product::findOrFail($data['product_id']);

        abort_unless($product->organisation_id === auth()->user()->current_organisation_id, 403);

        return $data;
    }
}
