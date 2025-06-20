<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, reset any existing default currencies
        DB::table('currencies')->update(['default' => 0]);
        
        // Check if TZS already exists
        $tzs = DB::table('currencies')->where('title', 'TZS')->first();
        
        if ($tzs) {
            // Update existing TZS entry
            DB::table('currencies')
                ->where('title', 'TZS')
                ->update([
                    'symbol' => 'TSh',
                    'rate' => 1.0,
                    'position' => 'before',
                    'default' => 1,
                    'active' => 1,
                ]);
        } else {
            // Insert TZS if it doesn't exist
            DB::table('currencies')->insert([
                'id' => 1,
                'title' => 'TZS',
                'symbol' => 'TSh',
                'rate' => 1.0,
                'position' => 'before',
                'default' => 1,
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Update USD to not be default
        DB::table('currencies')
            ->where('title', 'USD')
            ->update([
                'default' => 0,
                'rate' => 0.00043, // Approximate exchange rate (1 TZS = 0.00043 USD)
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to USD as default if needed
        DB::table('currencies')->update(['default' => 0]);
        
        DB::table('currencies')
            ->where('title', 'USD')
            ->update([
                'default' => 1,
                'rate' => 1.0,
            ]);
    }
};
