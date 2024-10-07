<?php

namespace App\Http\Requests\Central\Dashboard\Auth\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInfoRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:50'],
            'phone'      => ['required', 'string'],
            'email'      => 'required|email|unique:admins,email,' . $this->admin->id,
        ];
    }
}
