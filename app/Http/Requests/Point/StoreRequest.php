<?php
declare(strict_types=1);

namespace App\Http\Requests\Point;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'shop_id'   => ['required', Rule::exists('shops', 'id')],
            'type'      => 'string',
            'price'     => 'numeric',
            'value'     => 'integer',
            'active'    => 'boolean',
        ];
    }
}
