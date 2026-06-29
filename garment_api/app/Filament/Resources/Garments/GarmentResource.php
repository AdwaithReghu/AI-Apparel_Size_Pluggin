<?php

namespace App\Filament\Resources\Garments;

use App\Filament\Resources\Garments\GarmentResource\Pages;
use App\Models\Garment;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;


class GarmentResource extends Resource
{
    protected static ?string $model = Garment::class;
    protected static ?string $navigationLabel = 'Garments';

   public static function form(Schema $schema): Schema
{
    return $schema->components([
        \Filament\Forms\Components\TextInput::make('name')
            ->required(),
        \Filament\Forms\Components\TextInput::make('brand'),
        \Filament\Forms\Components\Select::make('category')
    ->options(function () {
        $dbCategories = \App\Models\Category::where('user_id', auth()->id())
            ->where('is_active', true)
            ->pluck('name', 'name')
            ->toArray();

        $defaultCategories = [
            'Shirt'    => 'Shirt',
            'T-Shirt'  => 'T-Shirt',
            'Jacket'   => 'Jacket',
            'Trousers' => 'Trousers',
            'Dress'    => 'Dress',
            'Skirt'    => 'Skirt',
            'Shorts'   => 'Shorts',
            'Sweater'  => 'Sweater',
            'Coat'     => 'Coat',
            'Other'    => 'Other',
        ];

        return array_merge($defaultCategories, $dbCategories);
    })
    ->searchable(),
        \Filament\Forms\Components\Select::make('size_label')
            ->options([
                'XS' => 'XS',
                'S'  => 'S',
                'M'  => 'M',
                'L'  => 'L',
                'XL' => 'XL',
            ]),
        \Filament\Forms\Components\TextInput::make('chest')->numeric(),
        \Filament\Forms\Components\TextInput::make('waist')->numeric(),
        \Filament\Forms\Components\TextInput::make('length')->numeric(),
        \Filament\Forms\Components\TextInput::make('shoulder')->numeric(),
        \Filament\Forms\Components\TextInput::make('sleeve')->numeric(),
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
                TextColumn::make('brand')
                    ->default('—'),
                TextColumn::make('category')
                    ->badge(),
                TextColumn::make('size_label')
                    ->badge()
                    ->label('Size'),
                TextColumn::make('chest')
                    ->suffix(' cm')
                    ->default('—'),
                TextColumn::make('waist')
                    ->suffix(' cm')
                    ->default('—'),
                TextColumn::make('length')
                    ->suffix(' cm')
                    ->default('—'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending'   => 'warning',
                        default     => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Scanned At'),
            ])
           ->recordAction(null)
->actions([
    \Filament\Actions\EditAction::make()
        ->url(fn (Garment $record): string =>
            static::getUrl('edit', ['record' => $record])),
    \Filament\Actions\DeleteAction::make()
        ->action(fn (Garment $record) => $record->delete()),
])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListGarments::route('/'),
            'create' => Pages\CreateGarment::route('/create'),
            'view'   => Pages\ViewGarment::route('/{record}'),
            'edit'   => Pages\EditGarment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return null;
    }
}