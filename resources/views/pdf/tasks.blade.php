<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Tasks</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #fff;
        }

        .header {
            background: #1e293b;
            color: #fff;
            padding: 24px 32px;
            margin-bottom: 28px;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .header h1 {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.3px;
            margin-bottom: 4px;
        }
        .header .subtitle { font-size: 11px; color: #94a3b8; }
        .header .meta { text-align: right; font-size: 10px; color: #94a3b8; }

        .content { padding: 0 32px 32px; }

        .section { margin-bottom: 22px; }
        .section-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-low       { background: #d1fae5; color: #065f46; }
        .badge-medium    { background: #fef3c7; color: #92400e; }
        .badge-high      { background: #fee2e2; color: #991b1b; }
        .badge-todo      { background: #f1f5f9; color: #475569; }
        .badge-in_progress { background: #dbeafe; color: #1e40af; }
        .badge-review    { background: #ede9fe; color: #5b21b6; }
        .badge-done      { background: #d1fae5; color: #065f46; }

        /* Summary stats */
        .stats-table { width: 100%; border-collapse: separate; border-spacing: 6px; }
        .stat-cell {
            text-align: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 8px;
            width: 25%;
        }
        .stat-number { font-size: 22px; font-weight: 700; color: #1e293b; }
        .stat-label  { font-size: 10px; color: #64748b; margin-top: 2px; }

        table.tasks-table { width: 100%; border-collapse: collapse; }
        table.tasks-table thead tr { background: #f8fafc; }
        table.tasks-table th {
            text-align: left;
            padding: 8px 10px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
        }
        table.tasks-table td {
            padding: 9px 10px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        table.tasks-table tr:last-child td { border-bottom: none; }
        table.tasks-table tr:nth-child(even) { background: #fafafa; }

        .task-title { font-weight: 500; color: #1e293b; }
        .task-project { font-size: 10px; color: #64748b; margin-top: 1px; }

        .overdue { color: #dc2626; font-weight: 600; }
        .warning { color: #d97706; font-weight: 600; }

        .no-tasks {
            text-align: center;
            color: #94a3b8;
            padding: 20px;
            font-style: italic;
        }

        .filter-chips { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 16px; }
        .filter-chip {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 3px 10px;
            font-size: 10px;
            color: #475569;
        }

        .footer {
            margin-top: 32px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="header-top">
            <div>
                <h1>{{ $isPm ? 'Laporan Team Tasks' : 'Laporan My Tasks' }}</h1>
                <div class="subtitle">
                    {{ $isPm ? 'Tasks dari semua project yang dikelola' : 'Tasks yang di-assign ke kamu' }}
                    &mdash; ProjectMgmt
                </div>
            </div>
            <div class="meta">
                Digenerate: {{ $generatedAt }}<br>
                Oleh: {{ $user->name }} ({{ $isPm ? 'Project Manager' : 'Developer' }})
            </div>
        </div>
    </div>

    <div class="content">

        {{-- Filter aktif --}}
        @php
            $statusLabels   = ['todo'=>'Todo','in_progress'=>'In Progress','review'=>'Review','done'=>'Done'];
            $priorityLabels = ['low'=>'Low','medium'=>'Medium','high'=>'High'];
        @endphp
        @if(array_filter($filters))
        <div class="section">
            <div class="section-title">Filter Diterapkan</div>
            <div class="filter-chips">
                @if(!empty($filters['status']))
                    <span class="filter-chip">Status: {{ $statusLabels[$filters['status']] ?? $filters['status'] }}</span>
                @endif
                @if(!empty($filters['priority']))
                    <span class="filter-chip">Priority: {{ $priorityLabels[$filters['priority']] ?? $filters['priority'] }}</span>
                @endif
                @if(!empty($filters['project_id']))
                    <span class="filter-chip">Project: {{ $tasks->first()?->project?->name ?? 'ID '.$filters['project_id'] }}</span>
                @endif
            </div>
        </div>
        @endif

        {{-- Ringkasan --}}
        @php
            $byStatus = $tasks->groupBy('status');
        @endphp
        <div class="section">
            <div class="section-title">Ringkasan ({{ $tasks->count() }} task)</div>
            <table class="stats-table">
                <tr>
                    @foreach(['todo'=>'Todo','in_progress'=>'In Progress','review'=>'Review','done'=>'Done'] as $key => $label)
                    <td class="stat-cell">
                        <div class="stat-number">{{ $byStatus->get($key, collect())->count() }}</div>
                        <div class="stat-label">{{ $label }}</div>
                    </td>
                    @endforeach
                </tr>
            </table>
        </div>

        {{-- Daftar Task --}}
        <div class="section">
            <div class="section-title">Daftar Task</div>

            @if($tasks->isEmpty())
                <div class="no-tasks">Tidak ada task ditemukan.</div>
            @else
            <table class="tasks-table">
                <thead>
                    <tr>
                        <th style="width:4%">#</th>
                        <th style="width:{{ $isPm ? '30%' : '36%' }}">Judul Task</th>
                        @if($isPm)
                        <th style="width:16%">Assignee</th>
                        @endif
                        <th style="width:18%">Project</th>
                        <th style="width:10%">Priority</th>
                        <th style="width:11%">Status</th>
                        <th style="width:11%">Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $i => $task)
                    @php
                        $isOverdue = $task->due_date && $task->due_date->isPast() && $task->status !== 'done';
                        $isWarning = !$isOverdue && $task->due_date && $task->due_date->diffInDays(now()) <= 3 && $task->status !== 'done';
                    @endphp
                    <tr>
                        <td style="color:#94a3b8">{{ $i + 1 }}</td>
                        <td>
                            <div class="task-title">{{ $task->title }}</div>
                        </td>
                        @if($isPm)
                        <td style="color:#64748b">{{ $task->assignee?->name ?? '-' }}</td>
                        @endif
                        <td style="color:#64748b">{{ $task->project?->name ?? '-' }}</td>
                        <td><span class="badge badge-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span></td>
                        <td>
                            <span class="badge badge-{{ $task->status }}">
                                {{ $statusLabels[$task->status] ?? $task->status }}
                            </span>
                        </td>
                        <td>
                            @if($task->due_date)
                                <span class="{{ $isOverdue ? 'overdue' : ($isWarning ? 'warning' : '') }}">
                                    {{ $task->due_date->format('d M Y') }}
                                </span>
                                @if($isOverdue)
                                    <br><span style="font-size:9px;color:#dc2626">Overdue</span>
                                @elseif($isWarning)
                                    <br><span style="font-size:9px;color:#d97706">Due soon</span>
                                @endif
                            @else
                                <span style="color:#94a3b8">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

        <div class="footer">
            Dokumen ini digenerate otomatis oleh sistem ProjectMgmt pada {{ $generatedAt }} &bull; Bersifat rahasia
        </div>

    </div>
</body>
</html>
