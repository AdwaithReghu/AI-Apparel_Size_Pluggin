<?php

namespace App\Filament\Resources\SizeCharts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SizeChartForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('brand_id')
                    ->numeric(),
                TextInput::make('category')
                    ->required(),
                TextInput::make('size_label')
                    ->required(),
                TextInput::make('chest_min')
                    ->numeric(),
                TextInput::make('chest_max')
                    ->numeric(),
                TextInput::make('waist_min')
                    ->numeric(),
                TextInput::make('waist_max')
                    ->numeric(),
                TextInput::make('length_min')
                    ->numeric(),
                TextInput::make('length_max')
                    ->numeric(),
                TextInput::make('shoulder_min')
                    ->numeric(),
                TextInput::make('shoulder_max')
                    ->numeric(),
                TextInput::make('sleeve_min')
                    ->numeric(),
                TextInput::make('sleeve_max')
                    ->numeric(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
