<?php

namespace App\Filament\Widgets;

use App\Jobs\AnalyzeDocumentJob;
use App\Jobs\ExtractDocumentTextJob;
use App\Models\Document;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class FailedDocumentsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Documents Needing Attention')
            ->query(Document::query()->where('processing_status', 'failed')->latest())
            ->columns([
                TextColumn::make('original_name')->label('Document'),
                TextColumn::make('matter.title')->label('Matter'),
                TextColumn::make('error_message')->limit(60)->color('danger'),
                TextColumn::make('updated_at')->since()->label('Failed'),
            ])
            ->actions([
                Action::make('retryExtraction')
                    ->label('Retry Extraction')
                    ->icon('heroicon-o-arrow-path')
                    ->size('sm')
                    ->visible(fn (Document $record) => empty($record->extracted_text))
                    ->requiresConfirmation()
                    ->action(function (Document $record) {
                        $record->update(['processing_status' => 'extracting', 'error_message' => null]);
                        ExtractDocumentTextJob::dispatch($record);
                        Notification::make()->title('Extraction retry queued')->success()->send();
                    }),

                Action::make('retryAnalysis')
                    ->label('Retry Analysis')
                    ->icon('heroicon-o-sparkles')
                    ->size('sm')
                    ->visible(fn (Document $record) => ! empty($record->extracted_text))
                    ->requiresConfirmation()
                    ->action(function (Document $record) {
                        $record->update(['error_message' => null]);
                        AnalyzeDocumentJob::dispatch($record);
                        Notification::make()->title('Analysis retry queued')->success()->send();
                    }),
            ])
            ->paginated(false)
            ->emptyStateHeading('No failed documents')
            ->emptyStateDescription('Everything is processing cleanly.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
