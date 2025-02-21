<?php

namespace App\Exceptions;

use App\Services\Response;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class ApiException extends Exception
{
    /**
     *
     * @param Throwable $exception
     * @param mixed $data
     * @param null|string $debugMessage
     * @return JsonResponse
     */
    public static function handleException(\Throwable $exception, mixed $data, ?string $debugMessage = null): JsonResponse
    {
        try {
            $exceptionCode = self::getExceptionCode($exception);
            $debugMessage = empty($debugMessage) ? 'internal_error - check logs' : $debugMessage;
            $requestId = uniqid();
            $stackTrace = $exception->getTrace();
            $formattedTrace = [];

            foreach ($stackTrace as $key => $stackPoint) {
                $trace = "#$key ";
                $trace .= (isset($stackPoint['file']) ? $stackPoint['file'] : 'unknown file');
                $trace .= ' ) @LINE ' . (isset($stackPoint['line']) ? $stackPoint['line'] : 'unknown line') . ': ';
                $trace .= (isset($stackPoint['class']) ? $stackPoint['class'] . '->' : '');
                $trace .= $stackPoint['function'] . '()';
                $formattedTrace[] = $trace;
            }

            $errorData = [
                'exceptionType' => get_class($exception),
                'message' => $exception->getMessage(),
                'debugMessage' => $debugMessage,
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
                'data' => json_encode($data),
                'statusCode' => $exceptionCode,
                'trace' => $formattedTrace,
            ];

            if ($exceptionCode !== 422) {
                Log::error($exception->getMessage(), $errorData);
            }

            if (env('APP_DEBUG', false) === false) {
                $errorData = [];
            }

            return match (get_class($exception)) {
                'Illuminate\Http\Exceptions\HttpResponseException',
                'Illuminate\Validation\ValidationException' => Response::getJsonResponse('error', null, null, $exceptionCode, $requestId, $errorData, $data['errors']),
                'Illuminate\Auth\AuthenticationException',
                'Laravel\Passport\Exceptions\OAuthServerException' => Response::getJsonResponse('error', 'unauthorized', null, $exceptionCode, $requestId, $errorData),
                'Symfony\Component\HttpKernel\Exception\NotFoundHttpException' => Response::getJsonResponse('error', $exception->getMessage(), null, $exceptionCode, $requestId, $errorData),
                'Illuminate\Database\Eloquent\ModelNotFoundException' => Response::getJsonResponse('error', 'Record not found', null, $exceptionCode, $requestId, $errorData),
                'App\Exceptions\ModelDependenciesException' => Response::getJsonResponse('error', 'Dependencies found', null, $exceptionCode, $requestId, $errorData, $exception->dependencies),
                'App\Exceptions\SingleFieldManualException' => Response::getJsonResponse('error', $exception->message, null, $exceptionCode, $requestId, $errorData),
                default => Response::getJsonResponse('error', 'An unexpected error has happened', null, $exceptionCode, $requestId, $errorData)
            };
        } catch (\Exception $e) {
            return Response::getJsonResponse('error', 'A failure in the error handling has occured - Error code A-0034', [], 500, $requestId, [$e->getMessage()]);
        }
    }

    private static function getExceptionCode(\Throwable $exception): int
    {
        return match (get_class($exception)) {
            'Illuminate\Database\Eloquent\ModelNotFoundException',
            'Symfony\Component\HttpKernel\Exception\NotFoundHttpException' => 404,
            'Illuminate\Http\Exceptions\HttpResponseException',
            'Illuminate\Validation\ValidationException',
            'App\Exceptions\ModelDependenciesException',
            'App\Exceptions\SingleFieldManualException' => 422,
            'Illuminate\Auth\AuthenticationException' => 401,
            default => 500
        };
    }
}
