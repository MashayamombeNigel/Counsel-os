<?php

namespace App\Filament\Resources\MatterResource\RelationManagers;

use App\Jobs\AnalyzeDocumentJob;
use App\Jobs\ExtractDocumentTextJob;
use App\Models\Document;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $recordTitleAttribute = 'original_name';

    // No create/delete — documents belong to the upload workflow, not admin entry.
    public function isReadOnly(): bool
    {
        return false; // keep retry actions enabled, just no create form
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('original_name')->label('Document'),
                TextColumn::make('document_type')->badge(),
                TextColumn::make('processing_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'uploaded'         => 'gray',
                        'extracting'       => 'info',
                        'analysis_pending' => 'warning',
                        'analyzed'         => 'success',
                        'failed'           => 'danger',
                        default            => 'gray',
                    }),
                TextColumn::make('created_at')->dateTime('M j, Y'),
            ])
            ->actions([
                Action::make('retryExtraction')
                    ->label('Retry Extraction')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->visible(fn (Document $record) => $record->processing_status === 'failed' && empty($record->extracted_text))
                    ->requiresConfirmation()
                    ->action(function (Document $record) {
                        $record->update(['processing_status' => 'extracting', 'error_message' => null]);
                        ExtractDocumentTextJob::dispatch($record);
                        Notification::make()->title('Extraction retry queued')->success()->send();
                    }),

                Action::make('retryAnalysis')
                    ->label('Retry Analysis')
                    ->icon('heroicon-o-sparkles')
                    ->color('danger')
                    ->visible(fn (Document $record) => $record->processing_status === 'failed' && ! empty($record->extracted_text))
                    ->requiresConfirmation()
                    ->action(function (Document $record) {
                        $record->update(['error_message' => null]);
                        AnalyzeDocumentJob::dispatch($record);
                        Notification::make()->title('Analysis retry queued')->success()->send();
                    }),
            ]);
    }
}
