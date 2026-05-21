<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ProjectsOverviewWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'Semua Project';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn(): Builder => Project::query()
                    ->withCount([
                        'tasks',
                        'tasks as tasks_done_count' => fn($q) => $q->where('status', 'done'),
                    ])
                    ->with('creator')
                    ->latest()
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Project')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->url(fn(Project $record): string => url("/projects/{$record->id}"))
                    ->openUrlInNewTab(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'planning'    => 'Planning',
                        'in_progress' => 'In Progress',
                        'on_hold'     => 'On Hold',
                        'completed'   => 'Completed',
                        default       => ucfirst($state),
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'in_progress' => 'success',
                        'on_hold'     => 'warning',
                        'completed'   => 'primary',
                        default       => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('priority')
                    ->label('Prioritas')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->color(fn(string $state): string => match ($state) {
                        'high'   => 'danger',
                        'medium' => 'warning',
                        'low'    => 'success',
                        default  => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('progress')
                    ->label('Progress')
                    ->getStateUsing(fn(Project $record): string => $record->progress . '%')
                    ->color(fn(Project $record): string => match (true) {
                        $record->progress >= 75 => 'success',
                        $record->progress >= 40 => 'warning',
                        default                 => 'danger',
                    })
                    ->badge()
                    ->sortable(query: fn(Builder $query, string $direction): Builder => $query),

                TextColumn::make('tasks_info')
                    ->label('Task')
                    ->getStateUsing(fn(Project $record): string => "{$record->tasks_done_count}/{$record->tasks_count}")
                    ->color('gray'),

                TextColumn::make('creator.name')
                    ->label('Project Manager')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('end_date')
                    ->label('Deadline')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn(Project $record): string => match (true) {
                        $record->end_date && $record->end_date->isPast() && $record->status !== 'completed' => 'danger',
                        $record->end_date && $record->end_date->diffInDays(now()) <= 7 && $record->status !== 'completed' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'planning'    => 'Planning',
                        'in_progress' => 'In Progress',
                        'on_hold'     => 'On Hold',
                        'completed'   => 'Completed',
                    ]),

                SelectFilter::make('priority')
                    ->label('Prioritas')
                    ->options([
                        'high'   => 'High',
                        'medium' => 'Medium',
                        'low'    => 'Low',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}
