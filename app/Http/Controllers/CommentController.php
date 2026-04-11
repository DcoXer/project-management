<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Task;
use App\Services\CommentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct(private CommentService $commentService) {}

    public function store(StoreCommentRequest $request, Task $task): RedirectResponse
    {
        $this->commentService->store($task, $request->user(), $request->body);

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    public function destroy(Request $request, Comment $comment): RedirectResponse
    {
        $this->authorize('delete', $comment);

        $this->commentService->destroy($comment);

        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}
