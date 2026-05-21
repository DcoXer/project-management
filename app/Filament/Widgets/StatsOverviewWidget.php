<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalProjects  = Project::count();
        $activeProjects = Project::where('status', 'in_progress')->count();
        $totalTasks     = Task::count();
        $pendingReview  = Task::where('status', 'review')->count();
        $totalPm        = User::where('role', 'project_manager')->count();
        $totalDev       = User::where('role', 'developer')->count();

        return [
            Stat::make('Total Projects', $totalProjects)
                ->description("{$activeProjects} sedang berjalan")
                ->descriptionIcon('heroicon-m-folder-open')
                ->color('primary'),

            Stat::make('Total Tasks', $totalTasks)
                ->description("{$pendingReview} menunggu review")
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('warning'),

            Stat::make('Project Managers', $totalPm)
                ->description('Pengelola project aktif')
                ->descriptionIcon('heroicon-m-user-circle')
                ->color('success'),

            Stat::make('Developers', $totalDev)
                ->description('Anggota tim developer')
                ->descriptionIcon('heroicon-m-code-bracket')
                ->color('info'),
        ];
    }
}
