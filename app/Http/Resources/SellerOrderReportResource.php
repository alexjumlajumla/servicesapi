<?php
declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SellerOrderReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Order|JsonResource $this */

        return [
            'created_at'                    => $this->created_at?->format('Y-m-d H:i:s') . 'Z',
            'total_price'                   => $this->total_price,
            'fm_total_price'                => $this->total_price - $this->delivery_fee,
        ];
    }
}
