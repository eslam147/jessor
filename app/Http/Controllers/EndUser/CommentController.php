<?php

namespace App\Http\Controllers\EndUser;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentsRequest;
use App\Models\Comment;
use App\Models\Teacher;
use App\Services\Comment\CommentService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct(
        private readonly CommentService $commentService
    ) {}
    public function index(Request $request, $teacherId)
    {
        $page = $request->page ?? 1;
        $comments = $this->commentService->Comments($teacherId);
        return view('web.comments.comments', compact('comments'));
    }

    public function getReplies($commentId)
    {
        $comments  = $this->commentService->getAll($commentId);
        $data = [
            'page' => request()->integer('page', 1),
            'replies' => $comments['replies'],
            'comment' => $comments['comment'],
        ];
        return view('web.comments.get_replaies_comment', $data)->render();
    }

    public function store(CommentsRequest $request, $teacherId)
    {
        $teacher = Teacher::find($teacherId);
        $image = $this->commentService->saveImage($request->file('image'));
        $this->commentService->saveComment($teacher, $request->msg, $image);
        return response()->json([]);
    }

    public function replay(CommentsRequest $request)
    {
        $image = $this->commentService->saveImage($request->file('image'));
        $comment = $this->commentService->findId($request->id);
        $user = $comment->commentator;
        $this->commentService->saveComment($comment, $request->msg, $image, $user);
    }

    public function countReplies($id)
    {
        $count = Comment::where('commentable_id', $id)->count();
        return response()->json($count);
    }

    public function edit($id)
    {
        $comment = $this->commentService->findId($id);
        return view('web.comments.edit', compact('comment'));
    }

    public function update(CommentsRequest $request)
    {
        $oldImg = filter_var($request->oldImg, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        $image = $this->commentService->saveImage($request->file('image'),$oldImg);
        $this->commentService->SereditComment($request->id, $request->msg, $image);
    }

    public function delete($commentId)
    {
        return view('web.comments.delete',compact('commentId'));
    }

    public function remove(Request $request)
    {
        $comment = $this->commentService->findId($request->id);
        $this->commentService->deleteReplies($comment);
        $comment->delete();
    }
}
