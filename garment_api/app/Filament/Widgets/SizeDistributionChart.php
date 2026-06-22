<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use App\Models\Garment;

class SizeDistributionChart extends ChartWidget
{
    protected ?string $heading = 'Size Distribution';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = '2';

    protected function getData(): array
    {
        $userId = Auth::id();
        $sizes  = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];

        $counts = Garment::where('user_id', $userId)
            ->whereIn('size_label', $sizes)
            ->selectRaw('size_label as size, COUNT(*) as count')
            ->groupBy('size_label')
            ->pluck('count', 'size');

        return [
            'datasets' => [
                [
                    'label'           => 'Garments',
                    'data'            => collect($sizes)
                        ->map(fn($s) => $counts->get($s, 0))
                        ->toArray(),
                    'backgroundColor' => [
                        '#6C63FF',
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#FF9F40',
                        '#9966FF',
                    ],
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $sizes,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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