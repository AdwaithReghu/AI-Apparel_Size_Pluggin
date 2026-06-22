<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\AnalyticsOverview;
use App\Filament\Widgets\ScansChart;
use App\Filament\Widgets\SizeDistributionChart;

class Dashboard extends BaseDashboard
{
    
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title = 'Dashboard';
    protected static ?int $navigationSort = 1;

    public function getColumns(): int | array
    {
        return 4;
    }

    public function getWidgets(): array
    {
        return [
            AnalyticsOverview::class,
            ScansChart::class,
            SizeDistributionChart::class,
        ];
    }
}