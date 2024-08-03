<?php
namespace App\Traits;

use App\Enums\Response\HttpResponseCode;


trait ApiResponse
{
    public function successResponse($data, $message = null, HttpResponseCode $code = HttpResponseCode::SUCCESS)
    {
        return response()->json([
            'error' => false,
            'message' => $message,
            'data' => $data,
            'code' => $code,
        ]);
    }

    public function errorResponse($data, $message = null, HttpResponseCode $code = HttpResponseCode::BAD_REQUEST)
    {
        return response()->json([
            'error' => true,
            'message' => $message,
            'code' => $code,
        ]);
    }
}