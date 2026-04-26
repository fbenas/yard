<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected array $originalDimensions = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->originalDimensions = $data['variant_dimensions'] ?? [];

        return $data;
    }

    protected function afterSave(): void
    {
        $newDimensions = $this->record->variant_dimensions ?? [];

        if ($this->normaliseDimensions($this->originalDimensions) !== $this->normaliseDimensions($newDimensions)) {
            $this->record->disableVariants();
        }
    }

    protected function normaliseDimensions(array $dimensions): array
    {
        return collect($dimensions)
            ->keys()
            ->sort()
            ->values()
            ->all();
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
