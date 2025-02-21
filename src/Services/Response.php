<?php

namespace App\Services;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class Response
{
    /**
     *
     * @param string $requestStatus
     * @param null|string $message
     * @param mixed $data
     * @param int $statusCode
     * @param string $requestId
     * @param array $errorData
     * @param mixed $errorBag
     * @return JsonResponse
     * @throws BindingResolutionException
     */
    public static function getJsonResponse(
        string $requestStatus,
        ?string $message,
        mixed $data,
        int $statusCode,
        string $requestId = '',
        array $errorData = [],
        mixed $errorBag = []
    ): JsonResponse {
        if ($requestStatus !== 'success' && $requestStatus !== 'failure' && $requestStatus !== 'warning') {
            self::failureMessage('this endpoint is misconfigured and returing an invalid request status value');
        }

        self::logError($statusCode, $errorBag);

        $requestId = request()->header('X-Request-ID');

        $jsonResponseData = [
            'requestId' => $requestId,
            'data' => $data,
            'status' => $requestStatus,
            'statusCode' => $statusCode
        ];

        if (! empty($message)) {
            $jsonResponseData['message'] = $message;
        }

        if (! empty($errorBag)) {
            $jsonResponseData['errors'] = $errorBag;
        }

        if (! empty($errorData)) {
            $jsonResponseData['errorData'] = $errorData;
        }

        return response()->json($jsonResponseData, $statusCode, [], JSON_PRETTY_PRINT);
    }

    /**
     * A simple Logging method based on specific HTTP return status codes
     *
     * @param  int  $statusCode  The HTTP statusCode used
     * @param  mixed  $data  The data that should be logged alongside it
     * @return void
     */
    private static function logError($statusCode, $errorData)
    {
        if ($statusCode === 422 || $statusCode === 418) {
            Log::notice('Validation Error', $errorData);
        }
    }

    /**
     * Wrapper to return a simple failure JSON response message without any data
     */
    public static function failureMessage(string $message): JsonResponse
    {
        return self::getJsonResponse('failure', $message, [], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
    }
}
