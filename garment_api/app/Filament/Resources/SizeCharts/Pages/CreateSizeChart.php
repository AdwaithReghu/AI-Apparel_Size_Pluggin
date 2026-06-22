<?php

namespace App\Filament\Resources\SizeCharts\SizeChartResource\Pages;

use App\Filament\Resources\SizeCharts\SizeChartResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSizeChart extends CreateRecord
{
    protected static string $resource = SizeChartResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}