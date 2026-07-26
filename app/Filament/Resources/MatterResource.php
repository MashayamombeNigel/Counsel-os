<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MatterResource\Pages;
use App\Models\Matter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MatterResource extends Resource
{
    protected static ?string $model = Matter::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Practice Management';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('client_id')
                ->relationship('client', 'name')
                ->required()
                ->searchable(),
            TextInput::make('title')->required()->maxLength(160),
            TextInput::make('practice_area')->maxLength(120),
            Select::make('status')
                ->options([
                    'open' => 'Open',
                    'in_review' => 'In Review',
                    'waiting_client' => 'Waiting Client',
                    'closed' => 'Closed',
                ])
                ->required(),
            Textarea::make('description')->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('client.name')->label('Client')->searchable(),
                TextColumn::make('practice_area'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'success',
                        'in_review' => 'warning',
                        'waiting_client' => 'info',
                        'closed' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('documents_count')->counts('documents')->label('Docs'),
                TextColumn::make('tasks_count')->counts('tasks')->label('Tasks'),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'open' => 'Open',
                    'in_review' => 'In Review',
                    'waiting_client' => 'Waiting Client',
                    'closed' => 'Closed',
                ]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMatters::route('/'),
            'create' => Pages\CreateMatter::route('/create'),
            'edit' => Pages\EditMatter::route('/{record}/edit'),
        ];
    }
}
