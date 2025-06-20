<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\v1\Rest;

use App\Helpers\ResponseError;
use App\Services\ProjectService\ProjectService;
use Illuminate\Http\JsonResponse;

class ProjectController extends RestBaseController
{

    public function licenceCheck(): JsonResponse
    {
        $response = (new ProjectService)->activationKeyCheck();

        $response = json_decode($response);

            return $this->successResponse(
                __('errors.' . ResponseError::NO_ERROR, locale: $this->language),
                $response
            );


        return $this->onErrorResponse(['code' => ResponseError::ERROR_403]);
    }
}
