<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasComments;

class Comment extends Model
{
    use HasComments;

    protected $fillable = [
        'comment',
        'user_id',
        'image',
        'file_type',
        'is_approved'
    ];

    protected $casts = [
        'is_approved' => 'boolean'
    ];

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function commentable()
    {
        return $this->morphTo();
    }

    public function commentator()
    {
        return $this->belongsTo($this->getAuthModelName(), 'user_id');
    }


    public function nestedReplies()
    {
        return $this->hasMany(config('comments.comment_class'), 'commentable_id')
            ->with(['nestedReplies' => function($query) { 
                $query->with('commentator'); 
            }, 'commentator']);
    }
    
    public function directReplies()
    {
        return $this->hasMany(config('comments.comment_class'), 'commentable_id')
            ->with(['nestedReplies', 'commentator']);
    }
    public function approve()
    {
        $this->update([
            'is_approved' => true,
        ]);

        return $this;
    }
    public function disapprove()
    {
        $this->update([
            'is_approved' => false,
        ]);

        return $this;
    }

    protected function getAuthModelName()
    {
        if (config('comments.user_model')) {
            return config('comments.user_model');
        }

        if (!is_null(config('auth.providers.users.model'))) {
            return config('auth.providers.users.model');
        }

        throw new Exception('Could not determine the commentator model name.');
    }

}
