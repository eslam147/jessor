<?php

namespace App\Http\Requests\Central\Dashboard\Auth\Profile;

use App\Rules\MatcholdPassword;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'current_password'     => ['required', 'current_password:web'],
            'new_password'         => ['required', 'min:8'],
            'new_confirm_password' => ['same:new_password'],
        ];
    }
}
