<?php
declare(strict_types=1);

namespace App\Http\Requests\Story;

use App\Http\Requests\BaseRequest;

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
            'product_id'    => 'exists:products,id',
            'active'        => 'boolean',
            'file_urls'     => 'required|array',
            'file_urls.*'   => 'required|string',
        ];
    }
}
