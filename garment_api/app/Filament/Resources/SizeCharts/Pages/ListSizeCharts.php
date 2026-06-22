<?php

namespace App\Filament\Resources\SizeCharts\SizeChartResource\Pages;

use App\Filament\Resources\SizeCharts\SizeChartResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSizeCharts extends ListRecords
{
    protected static string $resource = SizeChartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}