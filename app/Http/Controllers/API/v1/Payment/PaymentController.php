<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\v1\Payment;

use App\Http\Controllers\API\v1\Rest\RestBaseController;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Throwable;

class PaymentController extends RestBaseController
{
    use ApiResponse;
    
    /**
     * Handle successful response
     * 
     * @param string $message
     * @param array $data
     * @param int $code
     * @return JsonResponse
     */
    protected function successResponse(string $message, array $data = [], int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }
    
    /**
     * Handle error response
     * 
     * @param array $data
     * @param int $code
     * @return JsonResponse
     */
    protected function onErrorResponse(array $data, int $code = 400): JsonResponse
    {
        return response()->json([
            'status' => false,
            'code' => $data['code'] ?? $code,
            'message' => $data['message'] ?? 'An error occurred',
            'data' => []
        ], $code);
    }
    
    /**
     * Handle exceptions
     * 
     * @param Throwable $e
     * @return JsonResponse
     */
    protected function handleException(Throwable $e): JsonResponse
    {
        return $this->onErrorResponse([
            'code' => $e->getCode() ?: 500,
            'message' => $e->getMessage(),
            'trace' => config('app.debug') ? $e->getTraceAsString() : null
        ]);
    }
}
