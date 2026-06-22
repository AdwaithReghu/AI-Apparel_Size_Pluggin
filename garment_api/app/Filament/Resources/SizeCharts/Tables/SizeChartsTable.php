<?php

namespace App\Filament\Resources\SizeCharts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SizeChartsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('brand_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('category')
                    ->searchable(),
                TextColumn::make('size_label')
                    ->searchable(),
                TextColumn::make('chest_min')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('chest_max')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('waist_min')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('waist_max')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('length_min')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('length_max')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('shoulder_min')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('shoulder_max')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sleeve_min')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sleeve_max')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
