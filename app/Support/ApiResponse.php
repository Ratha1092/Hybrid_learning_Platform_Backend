<?php

namespace App\Support;

class ApiResponse
{
    public static function success($data = null, $message = 'Success', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public static function error($message = 'Error', int $code = 400, $errors = null)
    {
        $payload = [
            'success' => false,
            'message' => $message
        ];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $code);
    }
}
