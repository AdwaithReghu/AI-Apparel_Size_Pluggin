<?php

namespace App\Filament\Resources\SizeCharts;

use App\Filament\Resources\SizeCharts\SizeChartResource\Pages;
use App\Models\SizeChart;
use App\Models\Brand;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;

class SizeChartResource extends Resource
{
    protected static ?string $model = SizeChart::class;
    protected static ?string $navigationLabel = 'Size Charts';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            // Brand and Category
            \Filament\Forms\Components\Select::make('brand_id')
                ->label('Brand')
                ->options(Brand::where('is_active', true)->pluck('name', 'id'))
                ->searchable()
                ->required(),

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
    ->searchable()
    ->required(),

            \Filament\Forms\Components\Toggle::make('is_active')
                ->default(true)
                ->label('Active'),

            // Chest measurements
            \Filament\Forms\Components\TextInput::make('chest_min')
                ->numeric()
                ->suffix('cm')
                ->label('Chest Min'),
            \Filament\Forms\Components\TextInput::make('chest_max')
                ->numeric()
                ->suffix('cm')
                ->label('Chest Max'),

            // Waist measurements
            \Filament\Forms\Components\TextInput::make('waist_min')
                ->numeric()
                ->suffix('cm')
                ->label('Waist Min'),
            \Filament\Forms\Components\TextInput::make('waist_max')
                ->numeric()
                ->suffix('cm')
                ->label('Waist Max'),

            // Length measurements
            \Filament\Forms\Components\TextInput::make('length_min')
                ->numeric()
                ->suffix('cm')
                ->label('Length Min'),
            \Filament\Forms\Components\TextInput::make('length_max')
                ->numeric()
                ->suffix('cm')
                ->label('Length Max'),

            // Shoulder measurements
            \Filament\Forms\Components\TextInput::make('shoulder_min')
                ->numeric()
                ->suffix('cm')
                ->label('Shoulder Min'),
            \Filament\Forms\Components\TextInput::make('shoulder_max')
                ->numeric()
                ->suffix('cm')
                ->label('Shoulder Max'),

            // Sleeve measurements
            \Filament\Forms\Components\TextInput::make('sleeve_min')
                ->numeric()
                ->suffix('cm')
                ->label('Sleeve Min'),
            \Filament\Forms\Components\TextInput::make('sleeve_max')
                ->numeric()
                ->suffix('cm')
                ->label('Sleeve Max'),

            \Filament\Forms\Components\Hidden::make('user_id')
                ->default(fn() => auth()->id()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('brand.name')
                    ->label('Brand')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->badge()
                    ->sortable(),
                TextColumn::make('size_label')
                    ->badge()
                    ->label('Size')
                    ->sortable(),
                TextColumn::make('chest_min')
                    ->label('Chest')
                    ->formatStateUsing(fn ($record) =>
                        $record->chest_min && $record->chest_max
                            ? "{$record->chest_min}–{$record->chest_max} cm"
                            : '—'
                    ),
                TextColumn::make('waist_min')
                    ->label('Waist')
                    ->formatStateUsing(fn ($record) =>
                        $record->waist_min && $record->waist_max
                            ? "{$record->waist_min}–{$record->waist_max} cm"
                            : '—'
                    ),
                TextColumn::make('length_min')
                    ->label('Length')
                    ->formatStateUsing(fn ($record) =>
                        $record->length_min && $record->length_max
                            ? "{$record->length_min}–{$record->length_max} cm"
                            : '—'
                    ),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'Shirt'    => 'Shirt',
                        'T-Shirt'  => 'T-Shirt',
                        'Jacket'   => 'Jacket',
                        'Trousers' => 'Trousers',
                        'Dress'    => 'Dress',
                    ]),
                SelectFilter::make('size_label')
                    ->label('Size')
                    ->options([
                        'XS'   => 'XS',
                        'S'    => 'S',
                        'M'    => 'M',
                        'L'    => 'L',
                        'XL'   => 'XL',
                        'XXL'  => 'XXL',
                        'XXXL' => 'XXXL',
                    ]),
            ])
            ->actions([
                \Filament\Actions\EditAction::make()
                    ->url(fn (SizeChart $record): string =>
                        static::getUrl('edit', ['record' => $record])),
                \Filament\Actions\DeleteAction::make()
                    ->action(fn (SizeChart $record) => $record->delete()),
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
            'index'  => Pages\ListSizeCharts::route('/'),
            'create' => Pages\CreateSizeChart::route('/create'),
            'edit'   => Pages\EditSizeChart::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return null;
    }
}