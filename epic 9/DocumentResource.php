<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Jobs\AnalyzeDocumentJob;
use App\Jobs\ExtractDocumentTextJob;
use App\Models\Document;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Practice Management';

    public static function getGloballySearchableAttributes(): array
    {
        return ['original_name', 'matter.title'];
    }

    // Read-only-ish: no create form here, documents are uploaded
    // through the attorney-facing product UI, not the admin panel.
    // This resource exists for operational visibility and retry
    // actions, not for creating documents from scratch.
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('document_type')
                ->options([
                    'contract' => 'Contract',
                    'lease' => 'Lease',
                    'title_deed' => 'Title Deed',
                    'correspondence' => 'Correspondence',
                    'research' => 'Research',
                    'other' => 'Other',
                ])
                ->required(),
            Textarea::make('error_message')->rows(3)->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('original_name')->label('Document')->searchable(),
                TextColumn::make('matter.title')->label('Matter')->searchable(),
                TextColumn::make('document_type')->badge(),
                TextColumn::make('processing_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'uploaded' => 'gray',
                        'extracting' => 'info',
                        'analysis_pending' => 'warning',
                        'analyzed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('error_message')->limit(40)->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->dateTime('M j, Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('processing_status')->options([
                    'uploaded' => 'Uploaded',
                    'extracting' => 'Extracting',
                    'analysis_pending' => 'Analysis Pending',
                    'analyzed' => 'Analyzed',
                    'failed' => 'Failed',
                ]),
            ])
            ->actions([
                // Retry extraction - only meaningful when the document
                // failed before any text was ever extracted.
                Action::make('retryExtraction')
                    ->label('Retry Extraction')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->visible(fn (Document $record) => $record->processing_status === 'failed' && empty($record->extracted_text))
                    ->requiresConfirmation()
                    ->action(function (Document $record) {
                        $record->update(['processing_status' => 'extracting', 'error_message' => null]);
                        ExtractDocumentTextJob::dispatch($record);

                        Notification::make()
                            ->title('Extraction retry queued')
                            ->success()
                            ->send();
                    }),

                // Retry analysis - only meaningful when text exists but
                // the AI analysis step is what failed.
                Action::make('retryAnalysis')
                    ->label('Retry Analysis')
                    ->icon('heroicon-o-sparkles')
                    ->color('danger')
                    ->visible(fn (Document $record) => $record->processing_status === 'failed' && ! empty($record->extracted_text))
                    ->requiresConfirmation()
                    ->action(function (Document $record) {
                        $record->update(['error_message' => null]);
                        AnalyzeDocumentJob::dispatch($record);

                        Notification::make()
                            ->title('Analysis retry queued')
                            ->success()
                            ->send();
                    }),

                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
