<?php

namespace App\Filament\Resources\Garments\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GarmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('brand'),
                TextInput::make('category'),
                TextInput::make('size_label'),
                FileUpload::make('image_path')
                    ->image(),
                TextInput::make('chest')
                    ->numeric(),
                TextInput::make('waist')
                    ->numeric(),
                TextInput::make('length')
                    ->numeric(),
                TextInput::make('shoulder')
                    ->numeric(),
                TextInput::make('sleeve')
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
            ]);
    }
}
