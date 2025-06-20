<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Traits\Loggable;
use Illuminate\Database\Seeder;
use Throwable;

class CurrencySeeder extends Seeder
{
    use Loggable;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $currencies = [
            [
                'id' => 1,
                'symbol' => 'TSh',
                'title' => 'TZS',
                'rate' => 1.0,
                'position' => 'before',
                'default' => 1,
                'active' => 1,
            ],
            [
                'id' => 2,
                'symbol' => '$',
                'title' => 'USD',
                'rate' => 0.00043, // Approximate exchange rate (1 TZS = 0.00043 USD)
                'position' => 'before',
                'default' => 0,
                'active' => 1,
            ]
        ];

        foreach ($currencies as $currency) {
            try {
                Currency::updateOrCreate(['id' => $currency['id']], $currency);
            } catch (Throwable $e) {
                $this->error($e);
            }
        }

    }
}
