<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                Select::make('role')
                    ->options(['admin' => 'Admin', 'project_manager' => 'Project manager', 'developer' => 'Developer'])
                    ->default('developer')
                    ->required(),
                Select::make('specialization')
                    ->options(['backend' => 'Backend', 'frontend' => 'Frontend', 'ui/ux' => 'UI/UX'])
                    ->placeholder('Pilih specialization...')
                    ->visible(fn ($get) => $get('role') === 'developer'),
                TextInput::make('avatar'),
                TextInput::make('phone')
                    ->tel(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
            ]);
    }
}
