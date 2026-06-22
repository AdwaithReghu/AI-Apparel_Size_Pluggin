<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use App\Models\Scan;
use Carbon\Carbon;

class ScansChart extends ChartWidget
{
    protected ?string $heading = 'Scans per day';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 1;
    public ?string $filter = '7';

    protected function getFilters(): ?array
    {
        return [
            '7'  => 'Last 7 days',
            '15' => 'Last 15 days',
            '30' => 'Last 1 month',
        ];
    }

    protected function getData(): array
    {
        $userId = Auth::id();
        $days   = (int) $this->filter;
        $start  = now()->subDays($days - 1)->startOfDay();

        $dateRange = collect();
        for ($i = $days - 1; $i >= 0; $i--) {
            $dateRange->push(now()->subDays($i)->format('Y-m-d'));
        }

        $scans = Scan::where('user_id', $userId)
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        return [
            'datasets' => [
                [
                    'label'                => 'Scans',
                    'data'                 => $dateRange->map(
                        fn($d) => $scans->get($d, 0)
                    )->toArray(),
                    'borderColor'          => '#6363dc',
                    'backgroundColor'      => 'rgba(99, 99, 220, 0.08)',
                    'fill'                 => true,
                    'tension'              => 0.4,
                    'pointRadius'          => 3,
                    'pointBackgroundColor' => '#6363dc',
                    'borderWidth'          => 2,
                ],
            ],
            'labels' => $dateRange->map(
                fn($d) => Carbon::parse($d)->format('M j')
            )->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'x' => [
                    'grid'  => ['display' => false],
                    'ticks' => ['font' => ['size' => 11]],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'ticks'       => [
                        'stepSize' => 1,
                        'font'     => ['size' => 11],
                    ],
                    'grid' => [
                        'color' => 'rgba(128,128,128,0.1)',
                    ],
                ],
            ],
        ];
    }
}