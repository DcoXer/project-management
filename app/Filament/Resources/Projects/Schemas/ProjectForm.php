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
                Select::make('priority')
                    ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'])
                    ->default('medium')
                    ->required(),
                DatePicker::make('start_date'),
                DatePicker::make('end_date'),
                Select::make('created_by')
                    ->label('Project Manager')
                    ->relationship('creator', 'name', fn ($query) => $query->where('role', 'project_manager'))
                    ->required(),
            ]);
    }
}
