<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class AIModelManagement extends Page
{
    protected static ?string $navigationLabel = 'AI Model Management';
    protected static ?int $navigationSort = 3;
    protected string $view = 'filament.admin.pages.ai-model-management';

    public string $modelStatus = 'active';
    public string $modelVersion = 'v1.0.0';
    public int $totalPredictions = 0;
    public float $avgConfidence = 0;
    public array $recentPredictions = [];

    public function mount(): void
    {
        $this->totalPredictions = \App\Models\Scan::count();
        $this->avgConfidence    = 85.5; // placeholder
        $this->modelVersion     = 'v1.0.0';
        $this->modelStatus      = 'active';
    }

    public function triggerRetrain(): void
    {
        try {
            $response = Http::post(
                'http://127.0.0.1:8001/retrain',
                ['feedback' => []]
            );

            $result = $response->json();

            Notification::make()
                ->title('Retraining triggered successfully!')
                ->body($result['message'] ?? 'Model retraining queued')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Retraining failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}