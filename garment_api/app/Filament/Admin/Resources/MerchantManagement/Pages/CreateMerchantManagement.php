<?php

namespace App\Filament\Admin\Resources\MerchantManagement\Pages;

use App\Filament\Admin\Resources\MerchantManagementResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateMerchantManagement extends CreateRecord
{
    protected static string $resource = MerchantManagementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            $data['password'] = Hash::make('merchant123');
        }
        return $data;
    }
}