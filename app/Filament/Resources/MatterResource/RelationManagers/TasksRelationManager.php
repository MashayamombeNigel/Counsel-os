<?php

namespace App\Filament\Resources\MatterResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static ?string $recordTitleAttribute = 'title';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(160),
            Textarea::make('description')->rows(2),
            DatePicker::make('due_date'),
            Select::make('status')
                ->options(['open' => 'Open', 'in_progress' => 'In Progress', 'done' => 'Done'])
                ->required()
                ->default('open'),
            Select::make('priority')
                ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'])
                ->required()
                ->default('medium'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('due_date')->date('M j, Y'),
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
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }
}
