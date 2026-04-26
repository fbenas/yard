<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductVariantResource\Pages;
use App\Models\Product;
use App\Models\ProductVariant;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductVariantResource extends Resource
{
    protected static ?string $model = ProductVariant::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Product Variants';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Variant')
                ->schema([
                    Select::make('product_id')
                        ->label('Product')
                        ->options(fn () => Product::query()
                            ->where('organisation_id', auth()->user()->current_organisation_id)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all())
                        ->required()
                        ->live()
                        ->disabled(fn (string $operation) => $operation === 'edit'),

                    TextInput::make('name')
                        ->maxLength(255),

                    TextInput::make('status')
                        ->default('active')
                        ->required(),
                ]),

            Section::make('Option values')
                ->schema(function (callable $get): array {
                    $productId = $get('product_id');

                    if (! $productId) {
                        return [];
                    }

                    $product = Product::query()
                        ->with('variants')
                        ->find($productId);

                    if (! $product) {
                        return [];
                    }

                    return collect($product->variant_dimensions ?? [])
                        ->map(function ($label, $key) use ($product) {
                            $existingValues = $product->variants
                                ->pluck("option_values.{$key}")
                                ->filter()
                                ->unique()
                                ->sort()
                                ->values()
                                ->all();

                            return TextInput::make("option_values.{$key}")
                                ->label($label ?: $key)
                                ->datalist($existingValues)
                                ->required()
                                ->maxLength(255);
                        })
                        ->values()
                        ->all();
                }),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('option_values')
                    ->label('Options')
                    ->formatStateUsing(fn ($state) => collect($state ?? [])
                        ->map(fn ($value, $key) => "{$key}: {$value}")
                        ->implode(', ')),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('inventory_items_count')
                    ->counts('inventoryItems')
                    ->label('Inventory items'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('product', fn (Builder $query) => $query
                ->where('organisation_id', auth()->user()->current_organisation_id));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductVariants::route('/'),
            'create' => Pages\CreateProductVariant::route('/create'),
            'edit' => Pages\EditProductVariant::route('/{record}/edit'),
        ];
    }
}
