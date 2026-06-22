<?php

namespace App\Filament\Admin\Resources\MerchantManagement\Pages;

use App\Filament\Admin\Resources\MerchantManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMerchantManagement extends EditRecord
{
    protected static string $resource = MerchantManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}