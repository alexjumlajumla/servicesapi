<?php
declare(strict_types=1);

namespace App\Services\BackUpService;

use App\Models\BackupHistory;
use App\Services\CoreService;

class BackUpService extends CoreService
{
    protected function getModelClass(): string
    {
        return BackupHistory::class;
    }
}
