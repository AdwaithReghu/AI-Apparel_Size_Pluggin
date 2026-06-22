<?php

namespace App\Filament\Resources\Brands;

use App\Filament\Resources\Brands\BrandResource\Pages;
use App\Models\Brand;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;
    protected static ?string $navigationLabel = 'Brands';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            \Filament\Forms\Components\TextInput::make('website')
                ->maxLength(255),
            \Filament\Forms\Components\TextInput::make('country')
                ->maxLength(255),
            \Filament\Forms\Components\Textarea::make('sizing_philosophy')
                ->rows(4)
                ->label('Sizing Notes')
                ->columnSpanFull(),
            \Filament\Forms\Components\Toggle::make('is_active')
                ->default(true)
                ->label('Active'),
            \Filament\Forms\Components\Hidden::make('user_id')
                ->default(fn() => auth()->id()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('country')
                    ->default('—'),
                TextColumn::make('website')
                    ->default('—')
                    ->limit(30),
                TextColumn::make('garments_count')
                    ->counts('garments')
                    ->label('Garments')
                    ->badge(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Added'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make()
                    ->url(fn (Brand $record): string =>
                        static::getUrl('edit', ['record' => $record])),
                \Filament\Actions\DeleteAction::make()
                    ->action(fn (Brand $record) => $record->delete()),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'view'   => Pages\ViewBrand::route('/{record}'),
            'edit'   => Pages\EditBrand::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return null;
    }
}