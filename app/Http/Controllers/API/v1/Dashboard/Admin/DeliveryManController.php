<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository\UserRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DeliveryManController extends AdminBaseController
{

    public function __construct(private UserRepository $repository)
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param FilterParamsRequest $request
     * @return AnonymousResourceCollection
     */
    public function paginate(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $deliveryMans = $this->repository->deliveryMans($request->all());

        return UserResource::collection($deliveryMans);
    }

}
