<?php

namespace App\Filament\Widgets;

use App\Models\Document;
use App\Models\Matter;
use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $failedCount = Document::where('processing_status', 'failed')->count();

        return [
            Stat::make('Open Matters', Matter::where('status', '!=', 'closed')->count())
                ->icon('heroicon-o-scale')
                ->color('success'),

            Stat::make('Pending Tasks', Task::where('status', '!=', 'done')->count())
                ->icon('heroicon-o-clipboard-document-check')
                ->color('warning'),

            Stat::make('Documents Awaiting Processing', Document::whereIn('processing_status', ['uploaded', 'extracting', 'analysis_pending'])->count())
                ->icon('heroicon-o-clock')
                ->color('info'),

            Stat::make('Failed Documents', $failedCount)
                ->description($failedCount > 0 ? 'Needs attention - see below' : 'All clear')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($failedCount > 0 ? 'danger' : 'success'),
        ];
    }
}
