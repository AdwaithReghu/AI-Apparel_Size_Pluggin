<?php

namespace App\Filament\Resources\SizeCharts\Schemas;

use Filament\Forms\Components\Select;
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

                // ── Target-shopper attributes for the sizing model training (Workstream 2) ──
                Select::make('target_gender')
                    ->label('Target gender')
                    ->options(['men' => 'Men', 'women' => 'Women', 'unisex' => 'Unisex'])
                    ->helperText('Who this size is cut for — used to train the AI model.'),
                TextInput::make('weight_min')
                    ->label('Weight min (kg)')
                    ->numeric(),
                TextInput::make('weight_max')
                    ->label('Weight max (kg)')
                    ->numeric(),
                TextInput::make('age_min')
                    ->label('Age min')
                    ->numeric(),
                TextInput::make('age_max')
                    ->label('Age max')
                    ->numeric(),
                Select::make('body_types')
                    ->label('Body type(s) this size suits')
                    ->multiple()
                    ->options([
                        'slim'     => 'Slim',
                        'regular'  => 'Regular',
                        'athletic' => 'Athletic',
                        'curvy'    => 'Curvy',
                    ]),

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
