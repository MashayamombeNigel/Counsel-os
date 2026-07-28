<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Practice Management';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('matter_id')
                ->relationship('matter', 'title')
                ->required()
                ->searchable(),
            TextInput::make('title')->required()->maxLength(160),
            Textarea::make('description')->rows(2),
            DatePicker::make('due_date'),
            Select::make('status')
                ->options(['open' => 'Open', 'in_progress' => 'In Progress', 'done' => 'Done'])
                ->required(),
            Select::make('priority')
                ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('matter.title')->label('Matter')->searchable(),
                TextColumn::make('due_date')->date('M j, Y')->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open'        => 'gray',
                        'in_progress' => 'warning',
                        'done'        => 'success',
                        default       => 'gray',
                    }),
                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low'    => 'gray',
                        'medium' => 'warning',
                        'high'   => 'danger',
                        default  => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')->options(['open' => 'Open', 'in_progress' => 'In Progress', 'done' => 'Done']),
                SelectFilter::make('priority')->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High']),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('due_date', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit'   => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
