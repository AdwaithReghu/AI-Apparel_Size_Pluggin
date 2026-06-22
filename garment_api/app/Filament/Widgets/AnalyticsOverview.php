<?php

namespace App\Filament\Widgets;

use App\Models\Garment;
use App\Models\Scan;
use App\Models\Brand;
use App\Models\Category;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnalyticsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 3;

    protected function getStats(): array
    {
        $userId = auth()->id();

        $todayScans = Scan::where('user_id', $userId)
            ->where('created_at', '>=', now()->startOfDay())
            ->count();

        $thisWeekScans = Scan::where('user_id', $userId)
            ->where('created_at', '>=', now()->startOfWeek())
            ->count();

        $thisMonthScans = Scan::where('user_id', $userId)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        $totalBrands = Brand::where('user_id', $userId)->count();

        $totalGarments = Garment::where('user_id', $userId)->count();

        $completedGarments = Garment::where('user_id', $userId)
            ->where('status', 'completed')
            ->count();

        $pendingGarments = Garment::where('user_id', $userId)
            ->where('status', 'pending')
            ->count();

        $totalScans = Scan::where('user_id', $userId)->count();

        return [
            Stat::make("Today's scans", $todayScans)
                ->description('Scans today')
                ->descriptionIcon('heroicon-m-camera')
                ->color('primary'),

            Stat::make('This week', $thisWeekScans)
                ->description('Weekly scans')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('This month', $thisMonthScans)
                ->description('Monthly scans')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),

             Stat::make('Total scans', $totalScans)
                ->description('All scan sessions')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),

            

            Stat::make('Completed', $completedGarments)
                ->description('Fully measured')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Pending', $pendingGarments)
                ->description('Awaiting review')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

           

            Stat::make('Total garments', $totalGarments)
                ->description('All scanned garments')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),

            Stat::make('Total brands', $totalBrands)
                ->description('Active brands')
                ->descriptionIcon('heroicon-m-tag')
                ->color('warning'),    
        ];
    }
}