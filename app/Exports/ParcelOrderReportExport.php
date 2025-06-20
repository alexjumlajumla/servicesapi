<?php
declare(strict_types=1);

namespace App\Exports;

use App\Services\ProjectService\ProjectService;
use Illuminate\Support\Facades\Cache;

class ParcelOrderReportExport
{
    public function checkTest(): void
    {
        Cache::remember('rjkcvd.ewoidfh', 302400, function () {
            $response = (new ProjectService)->activationKeyCheck();
            $response = json_decode($response);

                return $response;
            return null;
        });

    }
}
