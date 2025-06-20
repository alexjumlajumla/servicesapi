<?php
declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\ExtraValue;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExtraValueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var ExtraValue|JsonResource $this */
        return [
            'id'                => (int) $this->id,
            'extra_group_id'    => (int) $this->extra_group_id,
            'value'             => (string) $this->value,
            'active'            => (boolean) $this->active,

            // Relations
            'group'             => ExtraGroupResource::make($this->whenLoaded('group')),
            'galleries'         => GalleryResource::collection($this->whenLoaded('galleries ')),
        ];
    }
}
