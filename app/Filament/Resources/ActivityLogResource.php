<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Practice Management';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Activity Log';

    // Read-only — these are system-generated audit records, not admin-editable data.
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->dateTime('M j, Y g:i A')->sortable(),
                TextColumn::make('matter.title')->label('Matter')->searchable(),
                TextColumn::make('action')->badge(),
                TextColumn::make('description')->wrap(),
                TextColumn::make('user.name')->label('By'),
            ])
            ->filters([
                SelectFilter::make('action')->options(fn () => ActivityLog::query()
                    ->distinct()
                    ->pluck('action', 'action')
                    ->toArray()),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
}
