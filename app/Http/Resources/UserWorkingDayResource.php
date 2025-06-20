<?php
declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\UserWorkingDay;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserWorkingDayResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var UserWorkingDay|JsonResource $this */
        return [
            'id'            => $this->when($this->id, $this->id),
            'day'           => $this->when($this->day, $this->day),
            'from'          => $this->when($this->from, $this->from),
            'to'            => $this->when($this->to, $this->to),
            'disabled'      => (boolean)$this->disabled,
            'created_at'    => $this->when($this->created_at, $this->created_at?->format('Y-m-d H:i:s') . 'Z'),
            'updated_at'    => $this->when($this->updated_at, $this->updated_at?->format('Y-m-d H:i:s') . 'Z'),

            'user'          => UserResource::make($this->whenLoaded('user')),
        ];
    }
}
