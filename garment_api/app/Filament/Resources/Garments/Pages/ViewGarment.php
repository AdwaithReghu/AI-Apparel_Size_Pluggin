<?php

namespace App\Filament\Resources\Garments\GarmentResource\Pages;

use App\Filament\Resources\Garments\GarmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGarment extends ViewRecord
{
    protected static string $resource = GarmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}