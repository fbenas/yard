<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryItemResource\Pages;
use App\Models\InventoryItem;
use App\Models\ProductVariant;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InventoryItemResource extends Resource
{
    protected static ?string $model = InventoryItem::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Inventory Items';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Inventory Item')
                ->schema([
                    Select::make('product_variant_id')
                        ->label('Variant')
                        ->options(fn () => ProductVariant::query()
                            ->whereHas('product', fn (Builder $query) => $query
                                ->where('organisation_id', auth()->user()->current_organisation_id))
                            ->with('product')
                            ->get()
                            ->mapWithKeys(fn (ProductVariant $variant) => [
                                $variant->id => trim($variant->product->name . ' / ' . ($variant->name ?? 'Variant')),
                            ])
                            ->all())
                        ->required(),

                    TextInput::make('sku')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('name')
                        ->maxLength(255),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('variant.product.name')
                    ->label('Product')
                    ->searchable(),

                TextColumn::make('variant.name')
                    ->label('Variant'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('variant.product', fn (Builder $query) => $query
                ->where('organisation_id', auth()->user()->current_organisation_id));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventoryItems::route('/'),
            'create' => Pages\CreateInventoryItem::route('/create'),
            'edit' => Pages\EditInventoryItem::route('/{record}/edit'),
        ];
    }
}
