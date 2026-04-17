<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $query = Project::with(['creator', 'members'])
            ->withCount([
                'tasks',
                'tasks as tasks_done_count' => fn ($q) => $q->where('status', 'done'),
            ])
            ->where(fn ($q) => $q
                ->where('created_by', $user->id)
                ->orWhereHas('members', fn ($m) => $m->where('user_id', $user->id))
            );

        if ($user->role === 'developer') {
            $query->where('status', '!=', 'planning');
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $projects = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'filters' => $request->only(['search', 'status', 'priority']),
        ]);
    }

    public function show(Request $request, Project $project): Response
    {
        $user = $request->user();

        $isMember  = $project->members()->where('user_id', $user->id)->exists();
        $isCreator = $project->created_by === $user->id;

        if (! $isMember && ! $isCreator) {
            abort(403, 'Kamu bukan anggota project ini.');
        }

        if ($user->role === 'developer' && $project->status === 'planning') {
            abort(403, 'Project belum dimulai oleh Project Manager.');
        }

        $project->loadCount([
            'tasks',
            'tasks as tasks_done_count' => fn ($q) => $q->where('status', 'done'),
        ]);
        $project->load(['creator', 'members', 'tasks.assignee']);

        $isPm = $project->members()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'manager')
            ->exists();

        return Inertia::render('Projects/Show', [
            'project' => $project,
            'is_pm'   => $isPm,
        ]);
    }

    public function start(Request $request, Project $project): RedirectResponse
    {
        $user = $request->user();

        $isPm = $project->members()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'manager')
            ->exists();

        if (! $isPm) {
            abort(403, 'Hanya Project Manager yang bisa memulai project.');
        }

        if ($project->status !== 'planning') {
            return back()->with('error', 'Project hanya bisa dimulai dari status Planning.');
        }

        $project->update(['status' => 'in_progress']);

        return back()->with('success', 'Project berhasil dimulai.');
    }

    public function exportPdf(Request $request, Project $project): HttpResponse
    {
        $user = $request->user();

        $isMember  = $project->members()->where('user_id', $user->id)->exists();
        $isCreator = $project->created_by === $user->id;

        if (! $isMember && ! $isCreator) {
            abort(403);
        }

        $project->loadCount([
            'tasks',
            'tasks as tasks_done_count' => fn ($q) => $q->where('status', 'done'),
        ]);
        $project->load(['creator', 'members', 'tasks.assignee']);

        $pdf = Pdf::loadView('pdf.project', [
            'project'   => $project,
            'generatedAt' => now()->format('d M Y, H:i'),
        ])->setPaper('a4', 'portrait');

        $filename = 'laporan-' . str($project->name)->slug() . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}
