<?php
namespace App\Enums\Lesson;

use App\Traits\EnumToArray;

enum LessonStatus: string
{
    use EnumToArray;
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
    case DRAFT = 'draft';
    public function color(){
        return match($this){
            self::PUBLISHED => 'success',
            self::ARCHIVED => 'warning',
            self::DRAFT => 'dark',
        };
    }
    public function translatedName(){
        return match($this){
            self::PUBLISHED => trans('published'),
            self::ARCHIVED => trans('archived'),
            self::DRAFT => trans('draft'),
        };
    }
}