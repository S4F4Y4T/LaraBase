<?php

namespace App\Traits\V1;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Return a success JSON response.
     *
     * @param null|array $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public static function success(string $message, int $code = 200, null|array|object $data = null): JsonResponse
    {
        // Build the response array
        $response = [
            'code' => $code,
            'type' => 'success',
            'message' => $message,
        ];

        // Conditionally add 'data' if it's not empty
        if (!empty($data)) {
            $response['data'] = $data;
        }

        // Return the JSON response
        return response()->json($response, $code);
    }


    /**
     * Return an error array response.
     *
     * @param string|array $error
     * @param int $code
     * @return JsonResponse
     */
    public static function error(string|array $message, int $code = 400, array $errors = []): JsonResponse
    {
        // Build the response array
        $response = [
            'code' => $code,
            'type' => 'error',
            'message' => $message,
        ];

        // Conditionally add 'errors' if it's not empty
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        // Return the JSON response
        return response()->json($response, $code);
    }

}
