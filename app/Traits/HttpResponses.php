<?php

namespace App\Traits;

trait HttpResponses
{
    protected function success($data, string $message = null, int $code = 200): array
    {
        return [
            'data' => $data ?: null,
            'message' => $message ?: null,
            'status' => $code
        ];

        // not used wrong response with "Content-Type" => "application/json"
        return response()
            ->json([
                'status' => 'Request was successfull.',
                'message' => $message,
                'data' => $data
            ], $code); // ['Content-Type' => 'application/json']
    }

    protected function error($data, string $message = null, int $code = 422): array
    {
        return [
            'data' => $data,
            'message' => $message,
            'status' => $code
        ];

        // not used wrong response with "Content-Type" => "application/json"
        return response()
            ->json()
            ->json([
                'status' => 'Error has occurred...',
                'message' => $message,
                'data' => $data
            ], $code); // ['Content-Type' => 'application/json']
    }
}
