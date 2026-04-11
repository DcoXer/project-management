<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Project - {{ $project->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #fff;
        }

        /* Header */
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
        .header .subtitle {
            font-size: 11px;
            color: #94a3b8;
        }
        .header .meta {
            text-align: right;
            font-size: 10px;
            color: #94a3b8;
        }

        /* Content wrapper */
        .content { padding: 0 32px 32px; }

        /* Section */
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

        /* Info grid */
        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .info-row { display: table-row; }
        .info-label {
            display: table-cell;
            width: 130px;
            color: #64748b;
            padding: 4px 0;
            vertical-align: top;
        }
        .info-value {
            display: table-cell;
            color: #1e293b;
            font-weight: 500;
            padding: 4px 0;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-planning  { background: #f1f5f9; color: #475569; }
        .badge-in_progress { background: #d1fae5; color: #065f46; }
        .badge-on_hold   { background: #fef3c7; color: #92400e; }
        .badge-completed { background: #d1fae5; color: #065f46; }
        .badge-low       { background: #d1fae5; color: #065f46; }
        .badge-medium    { background: #fef3c7; color: #92400e; }
        .badge-high      { background: #fee2e2; color: #991b1b; }
        .badge-todo      { background: #f1f5f9; color: #475569; }
        .badge-review    { background: #ede9fe; color: #5b21b6; }
        .badge-done      { background: #d1fae5; color: #065f46; }

        /* Progress bar */
        .progress-wrap {
            background: #f1f5f9;
            border-radius: 6px;
            height: 10px;
            overflow: hidden;
            margin-top: 6px;
        }
        .progress-bar {
            height: 10px;
            background: #1e293b;
            border-radius: 6px;
        }
        .progress-label {
            font-size: 11px;
            color: #64748b;
            margin-bottom: 4px;
        }

        /* Members */
        .members-list { display: flex; flex-wrap: wrap; gap: 8px; }
        .member-chip {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 4px 10px;
            font-size: 10px;
            color: #334155;
        }
        .member-chip .role {
            color: #94a3b8;
            margin-left: 4px;
        }

        /* Tasks table */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead tr {
            background: #f8fafc;
        }
        th {
            text-align: left;
            padding: 8px 10px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
        }
        td {
            padding: 9px 10px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        tr:last-child td { border-bottom: none; }
        tr:nth-child(even) { background: #fafafa; }
        .task-title { font-weight: 500; color: #1e293b; }
        .task-assignee { color: #64748b; font-size: 10px; }

        /* Summary stats */
        .stats-grid {
            display: table;
            width: 100%;
        }
        .stat-box {
            display: table-cell;
            text-align: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            width: 25%;
        }
        .stat-box + .stat-box { margin-left: 8px; }
        .stat-number { font-size: 22px; font-weight: 700; color: #1e293b; }
        .stat-label  { font-size: 10px; color: #64748b; margin-top: 2px; }

        /* Footer */
        .footer {
            margin-top: 32px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
        }

        .no-tasks {
            text-align: center;
            color: #94a3b8;
            padding: 20px;
            font-style: italic;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="header-top">
            <div>
                <h1>{{ $project->name }}</h1>
                <div class="subtitle">Laporan Project &mdash; ProjectMgmt</div>
            </div>
            <div class="meta">
                Digenerate: {{ $generatedAt }}<br>
                Dibuat oleh: {{ $project->creator->name }}
            </div>
        </div>
    </div>

    <div class="content">

        <!-- Info Umum -->
        <div class="section">
            <div class="section-title">Informasi Umum</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        <span class="badge badge-{{ $project->status }}">
                            {{ ['planning'=>'Planning','in_progress'=>'In Progress','on_hold'=>'On Hold','completed'=>'Completed'][$project->status] ?? $project->status }}
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Prioritas</div>
                    <div class="info-value">
                        <span class="badge badge-{{ $project->priority }}">
                            {{ ucfirst($project->priority) }}
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal Mulai</div>
                    <div class="info-value">{{ $project->start_date ? $project->start_date->format('d M Y') : '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal Selesai</div>
                    <div class="info-value">{{ $project->end_date ? $project->end_date->format('d M Y') : '-' }}</div>
                </div>
                @if($project->description)
                <div class="info-row">
                    <div class="info-label">Deskripsi</div>
                    <div class="info-value" style="color:#475569">{{ $project->description }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Progress -->
        <div class="section">
            <div class="section-title">Progress</div>
            <div class="progress-label">{{ $project->tasks_done_count }} dari {{ $project->tasks_count }} task selesai &mdash; {{ $project->progress }}%</div>
            <div class="progress-wrap">
                <div class="progress-bar" style="width: {{ $project->progress }}%"></div>
            </div>
        </div>

        <!-- Ringkasan Task -->
        @php
            $byStatus = $project->tasks->groupBy('status');
            $statuses = ['todo'=>'Todo','in_progress'=>'In Progress','review'=>'Review','done'=>'Done'];
        @endphp
        <div class="section">
            <div class="section-title">Ringkasan Task</div>
            <table>
                <tr>
                    @foreach($statuses as $key => $label)
                    <td style="width:25%; text-align:center; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px;">
                        <div style="font-size:22px; font-weight:700; color:#1e293b;">{{ $byStatus->get($key, collect())->count() }}</div>
                        <div style="font-size:10px; color:#64748b; margin-top:2px;">{{ $label }}</div>
                    </td>
                    @endforeach
                </tr>
            </table>
        </div>

        <!-- Members -->
        @if($project->members->count())
        <div class="section">
            <div class="section-title">Anggota Tim ({{ $project->members->count() }} orang)</div>
            <div class="members-list">
                @foreach($project->members as $member)
                <div class="member-chip">
                    {{ $member->name }}<span class="role">· {{ $member->pivot->role ?? '-' }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Daftar Task -->
        <div class="section">
            <div class="section-title">Daftar Task ({{ $project->tasks->count() }})</div>
            @if($project->tasks->isEmpty())
                <div class="no-tasks">Belum ada task di project ini.</div>
            @else
            <table>
                <thead>
                    <tr>
                        <th style="width:5%">#</th>
                        <th style="width:38%">Judul Task</th>
                        <th style="width:18%">Assignee</th>
                        <th style="width:13%">Priority</th>
                        <th style="width:13%">Status</th>
                        <th style="width:13%">Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($project->tasks as $i => $task)
                    <tr>
                        <td style="color:#94a3b8">{{ $i + 1 }}</td>
                        <td>
                            <div class="task-title">{{ $task->title }}</div>
                        </td>
                        <td class="task-assignee">{{ $task->assignee?->name ?? '-' }}</td>
                        <td><span class="badge badge-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span></td>
                        <td>
                            <span class="badge badge-{{ $task->status }}">
                                {{ ['todo'=>'Todo','in_progress'=>'In Progress','review'=>'Review','done'=>'Done'][$task->status] ?? $task->status }}
                            </span>
                        </td>
                        <td style="color:#64748b">{{ $task->due_date ? $task->due_date->format('d M Y') : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            Dokumen ini digenerate otomatis oleh sistem ProjectMgmt pada {{ $generatedAt }} &bull; Bersifat rahasia
        </div>

    </div>

</body>
</html>
