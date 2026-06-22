<?php

namespace App\Filament\Resources\Garments\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class GarmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('name'),
                TextEntry::make('brand')
                    ->placeholder('-'),
                TextEntry::make('category')
                    ->placeholder('-'),
                TextEntry::make('size_label')
                    ->placeholder('-'),
                ImageEntry::make('image_path')
                    ->placeholder('-'),
                TextEntry::make('chest')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('waist')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('length')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('shoulder')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('sleeve')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('status'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
