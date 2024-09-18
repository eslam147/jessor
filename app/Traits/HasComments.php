<?php

namespace App\Traits;


use Illuminate\Database\Eloquent\Model;
use BeyondCode\Comments\Contracts\Commentator;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasComments
{
    /**
     * Return all comments for this model.
     *
     * @return MorphMany
     */
    public function comments()
    {
        return $this->morphMany(config('comments.comment_class'), 'commentable')->with('commentator','directReplies');
    }

    /**
     * Attach a comment to this model.
     *
     * @param string $comment
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function comment(string|null $comment, ...$extraFields)
    {
        // تحويل الوسائط إلى مصفوفة مفاتيح وقيم
        $extraData = [];
        $status = false;
        $typeKey = config('comments.type_key');
        $imageKey = config('comments.image_key');
        $parentIdKey = config('comments.parent_id_key');
        $filtypeKey = config('comments.file_type_key');
        if(!empty($extraFields))
        {
            foreach($extraFields as $extraField)
            {
                if(is_array($extraField))
                {
                    $status = true;
                }
            }
            if ($status == true) {
                foreach(current($extraFields) as $key => $field)
                {
                    $extraData[$key] = $field;
                }
            } else {
                // إذا تم تمرير الوسائط بدلا من مصفوفة
                $extraData = [
                    $typeKey => $extraFields[0],
                    $imageKey => $extraFields[1] ?? null,
                    $parentIdKey => $extraFields[2] ?? null,
                    $filtypeKey => $extraFields[3] ?? null,
                ];
            }
        }
        return $this->commentAsUser(auth()->user(), $comment, $extraData);
    }
    
    /**
     * Attach a comment to this model as a specific user.
     *
     * @param Model|null $user
     * @param string $comment
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function commentAsUser(?Model $user, string|null $comment, array $extraFields = [])
    {
        $commentClass = config('comments.comment_class');
        $data = [
            'comment' => $comment,
            'is_approved' => ($user instanceof Commentator) ? ! $user->needsCommentApproval($this) : false,
            'user_id' => is_null($user) ? null : $user->getKey(),
            'commentable_id' => $this->getKey(),
            'commentable_type' => get_class(),
        ];
        if(!empty($extraFields))
        {
            foreach($extraFields as $key => $failed)
            {
                $data[$key] = $failed;
            }
        }
        $comment = new $commentClass($data);
        return $this->comments()->save($comment);
    }
}