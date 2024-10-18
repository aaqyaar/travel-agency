<?php

namespace App\Http\Utils;

class ApiResponse
{
    public static function success($message, $data = null, $code = 200)
    {   
        $result = [
            'status' => true,
            'message' => $message,
            'data' => $data,
        ];
        if ($data === null) {
            unset($result['data']);
        }
        return response()->json($result, $code);
    }

    public static function error($message, $error = null, $code = 400)
    {
        $result = [
            'status' => false,
            'message' => $message,
            'errors' => $error,
        ];
        if ($error === null) {
            unset($result['errors']);
        }
        return response()->json($result, $code);
    }
}