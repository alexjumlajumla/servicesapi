<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSelcomPaymentMethod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the payment method already exists
        $existingPayment = DB::table('payments')
            ->where('tag', 'selcom')
            ->first();

        if (!$existingPayment) {
            // Insert Selcom payment method
            $paymentId = DB::table('payments')->insertGetId([
                'tag' => 'selcom',
                'input' => 1, // Input type 1 for online payment
                'sandbox' => 1, // Set to 0 for production
                'active' => 1, // Enable by default
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert default payment payload
            DB::table('payment_payloads')->insert([
                'payment_id' => $paymentId,
                'payload' => json_encode([
                    'selcom_vendor_id' => env('SELCOM_VENDOR_ID', ''),
                    'selcom_key' => env('SELCOM_API_KEY', ''),
                    'selcom_secret' => env('SELCOM_API_SECRET', ''),
                    'currency' => 'TZS',
                    'is_live' => env('SELCOM_IS_LIVE', false),
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove the payment method and its payload
        $payment = DB::table('payments')
            ->where('tag', 'selcom')
            ->first();

        if ($payment) {
            DB::table('payment_payloads')
                ->where('payment_id', $payment->id)
                ->delete();

            DB::table('payments')
                ->where('id', $payment->id)
                ->delete();
        }
    }
}
