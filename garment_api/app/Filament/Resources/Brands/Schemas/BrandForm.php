<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BrandForm
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
                TextInput::make('slug')
                    ->required(),
                TextInput::make('logo_path'),
                Textarea::make('sizing_philosophy')
                    ->columnSpanFull(),
                TextInput::make('website')
                    ->url(),
                TextInput::make('country'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
