<?php

namespace App\Http\Requests;

use App\Rules\FilterWordsRule;
use App\Rules\ValidMessageContent;
use Illuminate\Foundation\Http\FormRequest;

class CommentsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        if(!request()->hasFile('image'))
        {
            $rules = [
                'msg' => ['required',new ValidMessageContent, new FilterWordsRule],
            ];
        }
        else
        {
            $rules = [
                'msg' => [new ValidMessageContent, new FilterWordsRule],
                'image' => 'image|mimes:jpeg,png,jpg,gif,pmb|max:2048'
            ];
        }
        return $rules;
    }
    public function messages()
    {
        return [
            'msg.required' => 'The message field is required.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, bmp.',
            'image.max' => 'The image size may not be greater than 2MB.',
        ];
    }
}