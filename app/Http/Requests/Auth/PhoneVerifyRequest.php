<?php
declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class PhoneVerifyRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules(): array
	{
		return [
            'verifyId' => [request('type') !== 'firebase' ? 'required' : 'nullable'],
            'phone'    => 'required|numeric',
            'id'       => 'required|string',
		];
	}
}
