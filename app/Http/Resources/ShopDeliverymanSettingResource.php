<?php
declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\ShopDeliverymanSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopDeliverymanSettingResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var ShopDeliverymanSetting|JsonResource $this */
        return [
            'id'            => $this->when($this->id,         $this->id),
            'shop_id'       => $this->when($this->shop_id,    $this->shop_id),
            'value'         => $this->when($this->value,      $this->value),
            'period'        => $this->when($this->period,     $this->period),

            //Relations
            'shop'          => ShopResource::make($this->whenLoaded('shop')),
        ];
    }
}
