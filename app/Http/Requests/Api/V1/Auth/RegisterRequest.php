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
            'mobile' => 'nullable|digits:11|numeric|regex:/^01[0-2,5]{1}[0-9]{8}$/',
            // -------------------------------------------- \\
            'class_section_id' => 'required|exists:class_sections,id',
            'category_id' => 'required|exists:categories,id',
            'email_addreess' => 'required|email|unique:users,email|different:guardian_email|different:father_email|different:mother_email',
            // -------------------------------------------- \\
            'parent' => 'required_without:guardian',
            'guardian' => 'required_without:parent',
            'password' => 'required|min:6|confirmed',
        ];
        // $rules['gender'] = 'nullable|string|in:male,female';

        if (isset($this->father_email)) {
            $rules['father_first_name'] = 'required|string|min:3|max:255';
            $rules['father_last_name'] = 'required|string|min:3|max:255';
            // ----------------------------------------------------------------- \\
            $rules['father_email'] = 'required|different:guardian_email|different:mother_email|different:email_addreess|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users,email|unique:parents,email';
            $rules['father_mobile'] = 'required|numeric|regex:/^[0-9]{7,16}$/|unique:users,mobile|unique:parents,mobile';
        }

        // ----------------------------------------------------------------- \\
        if (isset($this->mother_email)) {
            $rules['mother_first_name'] = 'required|string|min:3|max:255';
            $rules['mother_last_name'] = 'required|string|min:3|max:255';
            // ----------------------------------------------------------------- \\
            $rules['mother_mobile'] = 'required|numeric|regex:/^[0-9]{7,16}$/|unique:users,mobile|unique:parents,mobile';
            $rules['mother_email'] = 'required|different:guardian_email|different:father_email|different:email_addreess|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users,email|unique:parents,email';
        }
        // ----------------------------------------------------------------- \\


        if (isset($this->guardian_email)) {
            $this->guardianRules($rules);
        }
        return $rules;
    }
    public function messages()
    {
        return [
            'mobile.regex' => 'The mobile number format is invalid. It must start with "01" followed by a digit (0, 1, 2, or 5) and contain a total of 11 digits.',
        ];
    }
    private function guardianRules(&$rules)
    {
        $rules['guardian_email'] = 'required|different:mother_email|different:father_email|different:email_addreess|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:parents,email';
        $rules['guardian_gender'] = 'required|string|in:male,female';
        // ----------------------------------------------------------------- \\
        $rules['guardian_first_name'] = 'required|string|min:3|max:255';
        $rules['guardian_last_name'] = 'required|string|min:3|max:255';
        return $rules;
    }
}
