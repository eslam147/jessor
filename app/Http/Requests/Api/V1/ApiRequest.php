<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

use Illuminate\Http\Exceptions\HttpResponseException;

abstract class ApiRequest extends FormRequest
{
    protected function transformErrors($errors)
    {
        $newErrors = [];
        foreach ($errors as $field => $message) {
            $newErrors[$field] = $message[0];
        }
        return $newErrors;
    }
    abstract public function authorize();


    abstract public function rules();

    protected function failedValidation(Validator $validator)
    {

        throw new HttpResponseException(
            response()->json([
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            ])
        );
    }
}
