<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'planning' => 'Planning',
                        'in_progress' => 'In progress',
                        'on_hold' => 'On hold',
                        'completed' => 'Completed',
                    ])
                    ->default('planning')
                    ->required(),
                Select::make('priority')
                    ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'])
                    ->default('medium')
                    ->required(),
                DatePicker::make('start_date'),
                DatePicker::make('end_date'),
                Select::make('created_by')
                    ->label('Created By')
                    ->relationship('creator', 'name')
                    ->required(),
            ]);
    }
}
