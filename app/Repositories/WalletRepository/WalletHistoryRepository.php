<?php
declare(strict_types=1);

namespace App\Repositories\WalletRepository;

use App\Models\WalletHistory;
use App\Repositories\CoreRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Schema;

class WalletHistoryRepository extends CoreRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return WalletHistory::class;
    }

    public function walletHistoryPaginate(array $filter): LengthAwarePaginator
    {
        $column = $filter['column'] ?? 'id';

        if ($column !== 'id') {
            $column = Schema::hasColumn('wallet_histories', $column) ? $column : 'id';
        }

        return $this->model()
            ->with([
                'author',
                'user',
                'transaction.paymentSystem'
            ])
            ->when(array_key_exists('wallet_uuid', $filter), function ($q) use ($filter) {
                $q->where('wallet_uuid', data_get($filter, 'wallet_uuid'))->whereNotNull('wallet_uuid');
            })
            ->when(data_get($filter, 'status'), function ($q, $status) {
                $q->where('status', $status);
            })
            ->when(data_get($filter, 'type'), function ($q, $type) {
                $q->where('type', $type);
            })
            ->orderBy($column, $filter['sort'] ?? 'desc')
            ->paginate($filter['perPage'] ?? 10);
    }
}
