<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class TeamMembersWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Project Managers & Developers';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn(): Builder => User::query()
                    ->whereIn('role', ['project_manager', 'developer'])
                    ->withCount([
                        'tasks as active_tasks_count' => fn($q) => $q->whereNotIn('status', ['done']),
                    ])
                    ->orderByRaw("FIELD(role, 'project_manager', 'developer')")
                    ->orderBy('name')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn(User $record): string => $record->email),

                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'project_manager' => 'Project Manager',
                        'developer'       => 'Developer',
                        default           => ucfirst($state),
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'project_manager' => 'primary',
                        'developer'       => 'success',
                        default           => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('specialization')
                    ->label('Spesialisasi')
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'frontend'   => 'Frontend Dev',
                        'backend'    => 'Backend Dev',
                        'ui_ux'      => 'UI/UX Designer',
                        'qa'         => 'QA Engineer',
                        'devops'     => 'DevOps',
                        'mobile'     => 'Mobile Dev',
                        'pentesting' => 'Pentesting',
                        default      => '—',
                    })
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('active_tasks_count')
                    ->label('Task Aktif')
                    ->sortable()
                    ->badge()
                    ->color(fn(User $record): string => match (true) {
                        $record->active_tasks_count >= 5 => 'danger',
                        $record->active_tasks_count >= 3 => 'warning',
                        default                          => 'success',
                    }),

                TextColumn::make('phone')
                    ->label('No. HP')
                    ->default('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'project_manager' => 'Project Manager',
                        'developer'       => 'Developer',
                    ]),

                SelectFilter::make('specialization')
                    ->label('Spesialisasi')
                    ->options([
                        'frontend'   => 'Frontend Dev',
                        'backend'    => 'Backend Dev',
                        'ui_ux'      => 'UI/UX Designer',
                        'qa'         => 'QA Engineer',
                        'devops'     => 'DevOps',
                        'mobile'     => 'Mobile Dev',
                        'pentesting' => 'Pentesting',
                    ]),
            ])
            ->defaultSort('role')
            ->paginated([15, 30, 50]);
    }
}
