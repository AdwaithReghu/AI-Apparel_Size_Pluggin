<?php

namespace App\Filament\Resources\SizeCharts\SizeChartResource\Pages;

use App\Filament\Resources\SizeCharts\SizeChartResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSizeChart extends EditRecord
{
    protected static string $resource = SizeChartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}