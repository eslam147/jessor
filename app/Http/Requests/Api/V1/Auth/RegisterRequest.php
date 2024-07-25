<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Http\Requests\Api\V1\ApiRequest;

class RegisterRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile' => 'nullable|numeric|regex:/^[0-9]{7,16}$/',

            'class_section_id' => 'required|exists:class_sections,id',
            'category_id' => 'required|exists:categories,id',
            'email_addreess' => 'required|unique:users,email',
            // 'admission_date' => 'required',

            'parent' => 'required_without:guardian',
            'guardian' => 'required_without:parent',
            'password' => 'required|min:6|confirmed',
        ];

        if (isset($this->father_email)) {
            $rules['father_first_name'] = 'required|string|min:3|max:255';
            $rules['father_last_name'] = 'required|string|min:3|max:255';

            // $rules['father_name'] = 'required|string|min:3|max:255';
            $rules['father_email'] = 'required|different:guardian_email|different:mother_email|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users,email|unique:parents,email';
        }

        if (isset($this->mother_email)) {
            $rules['mother_first_name'] = 'required|string|min:3|max:255';
            $rules['mother_last_name'] = 'required|string|min:3|max:255';


            $rules['mother_email'] = 'required|different:guardian_email|different:father_email|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users,email|unique:parents,email';
        }
        if (isset($this->guardian_email)) {
            $rules['guardian_email'] = 'required|different:mother_email|different:father_email|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:parents,email';
            $rules['guardian_first_name'] = 'required|string|min:3|max:255';
            $rules['guardian_last_name'] = 'required|string|min:3|max:255';

        }
        return $rules;
    }
    public function messages()
    {
        return [
            'mobile.regex' => 'The mobile number must be a length of 7 to 15 digits.'
        ];
    }
}
