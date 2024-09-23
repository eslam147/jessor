<?php

namespace App\Services\Comment;

use App\Http\Requests\CommentsRequest;
use App\Models\Comment;
use App\Models\Teacher;
use App\Models\User;
use App\Services\Media\TenantMediaService;
use App\Traits\HasComments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentService 
{
    use HasComments;
    public function Comments($id)
    {
        $teacher = Teacher::find(trim($id));
        $comments = $teacher->comments()->orderByDesc('id')->paginate(10);
        return $comments;
    }
    public function getAll($commentId)
    {
        $replies = Comment::find($commentId);
        $replies = $replies->comments()->orderByDesc('id')->paginate(10);
        $comment = Comment::where('id', trim($commentId))->with('commentator')->first();
        return ['replies' => $replies, 'comment' => $comment];
    }
    public function store_comment(CommentsRequest $request)
    {
        $teacher = Teacher::find(trim($request->id));
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $newImage = TenantMediaService::uploadImage($image, "comment/image");
            $teacher->comment($request->msg, [
                'type' => 'comment',
                'image' => $newImage,
                'parent_id' => null,
                'file_type' => 'image'
            ]);
        } else {
            $teacher->comment($request->msg);
        }
    }
    public function findId($id){
        return Comment::where('id', $id)->with('commentator','directReplies')->first();
    }
    public function saveImage($image,$oldImage = null){
        if ($image) {
            $newImage = TenantMediaService::uploadImage($image, "comment/image",$oldImage);
            return [
                'image' => $newImage,
                'file_type' => 'image'
            ];
        }
        return null;
    }
    public function saveComment($comment,$msg,$image, $user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }
        if ($image) {
            $comment->commentAsUser(
                $user,
                $msg,
                [
                    'image' => $image,
                    'file_type' => 'image'
                ]
            );
        } else {
            $comment->commentAsUser($user,$msg);
        }
    }

    public function SereditComment($commentId,$msg,$image)
    {
        if ($image) {
            $this->editComment(
                $commentId,
                $msg,
                $image
            );
        } else {
            $this->editComment($commentId,$msg);
        }
    }

    public function deleteReplies($comment)
    {
        if ($comment->directReplies()->count()) 
        {
            $replies = $comment->directReplies;
            foreach ($replies as $reply) {
                $reply = $this->findId($reply->id);            
                $this->deleteReplies($reply);
                $reply->delete();
            }
        }
    }
}