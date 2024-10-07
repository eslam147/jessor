<?php

namespace App\Http\Requests\Dashboard\Student;

use App\Http\Requests\Api\V1\ApiRequest;

class StudentStoreRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        $studentRules = [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'student_password' => 'required|string|min:6',
            'gender' => 'required|string',
            'student_email' => 'required|email|unique:users,email',
        ];
        return array_merge($studentRules, $this->parentRules(), $this->guardianRules());
    }
    private function parentRules()
    {
        if (isset($request->parent)) {
            return [
                //father
                'father_email' => 'required|email',
                'father_first_name' => 'required|string',
                'father_last_name' => 'required|string',
                'father_mobile' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15',
                'father_password' => 'required|string|min:6',
                //mother
                'mother_email' => 'required|email|different:father_email',
                'mother_first_name' => 'required|string',
                'mother_last_name' => 'required|string',
                'mother_mobile' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15',
                'mother_password' => 'required|string|min:6',
            ];
        }
        return [];
    }
    private function guardianRules()
    {
        if (isset($request->parent)) {
            return [
                'guardian_email' => 'required|email',
                'guardian_first_name' => 'required|string',
                'guardian_last_name' => 'required|string',
                'guardian_mobile' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15',
                'guardian_password' => 'required|string|min:6',
            ];
        }
        return [];
    }

}
