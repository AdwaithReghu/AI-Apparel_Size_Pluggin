<?php

namespace App\Filament\Resources\Categories\CategoryResource\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()->hidden();
    }

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