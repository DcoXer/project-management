<?php

namespace App\Filament\Resources\Tasks\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('project_id')
                    ->label('Project')
                    ->relationship('project', 'name')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(['todo' => 'Todo', 'in_progress' => 'In progress', 'review' => 'Review', 'done' => 'Done'])
                    ->default('todo')
                    ->required(),
                Select::make('priority')
                    ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'])
                    ->default('medium')
                    ->required(),
                Select::make('assigned_to')
                    ->label('Assigned To')
                    ->relationship('assignee', 'name')
                    ->required(),
                Select::make('created_by')
                    ->label('Created By')
                    ->relationship('creator', 'name')
                    ->required(),
                DatePicker::make('due_date'),
            ]);
    }
}
