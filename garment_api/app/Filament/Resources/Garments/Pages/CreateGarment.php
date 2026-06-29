<?php

namespace App\Filament\Resources\Garments\GarmentResource\Pages;

use App\Filament\Resources\Garments\GarmentResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateGarment extends CreateRecord
{
    protected static string $resource = GarmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }

    // Remove "Create & create another" button
    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()->hidden();
    }

    // Add Clear button
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            Action::make('clear')
                ->label('Clear')
                ->color('gray')
                ->action(function () {
                    $this->form->fill([]);
                }),
            $this->getCancelFormAction(),
        ];
    }
}