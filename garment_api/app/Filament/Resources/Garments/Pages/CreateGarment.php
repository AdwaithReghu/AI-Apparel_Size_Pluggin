<?php

namespace App\Filament\Resources\Garments\GarmentResource\Pages;

use App\Filament\Resources\Garments\GarmentResource;

class CreateGarment extends \Filament\Resources\Pages\CreateRecord
{
    protected static string $resource = GarmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}