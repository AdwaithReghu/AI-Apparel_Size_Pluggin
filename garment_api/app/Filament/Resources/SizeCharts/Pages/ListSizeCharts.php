<?php

namespace App\Filament\Resources\SizeCharts\SizeChartResource\Pages;

use App\Filament\Resources\SizeCharts\SizeChartResource;
use App\Services\SizingModelRebuilder;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListSizeCharts extends ListRecords
{
    protected static string $resource = SizeChartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('rebuildModel')
                ->label('Rebuild sizing model')
                ->icon('heroicon-o-cpu-chip')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Rebuild the AI sizing model?')
                ->modalDescription('This retrains the sizing model on all your active size charts so every scanned brand is recognised. It can take up to a minute or two.')
                ->action(function () {
                    $result = app(SizingModelRebuilder::class)->rebuildForUser(auth()->user());

                    if ($result['success']) {
                        Notification::make()
                            ->title('Sizing model rebuilt')
                            ->body(sprintf(
                                'Trained %d brand(s) from %s rows.',
                                count($result['brands_trained'] ?? []),
                                number_format($result['rows_generated'] ?? 0)
                            ))
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Rebuild failed')
                            ->body($result['message'] ?? 'Unknown error')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}