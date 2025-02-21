<?php

namespace App\Traits;

use App\Exceptions\ApiException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;

trait HasRequestResource {
    /**
     * How to use it in Requests, just copy and paste the code bellow in your controller files
     * use HasRequestResource; (This file should be in Request files)
     */


    /**
     *
     * @param array $errors
     * @return JsonResponse
     * @throws BindingResolutionException
     */
    public function response(array $errors): JsonResponse
    {
        $transformed = [];

        foreach ($errors as $field => $message) {
            $transformed[$field][] = $message[0];
        }

        return ApiException::handleException(new HttpResponseException(response()->json([])), ['errors' => $transformed]);
    }

    /**
     * Failed validation disable redirect
     */
    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException($this->response($validator->errors()->jsonSerialize()));
    }
}