<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimestampsToPaymentPayloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('payment_payloads', 'created_at')) {
            Schema::table('payment_payloads', function (Blueprint $table) {
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasColumn('payment_payloads', 'updated_at')) {
            Schema::table('payment_payloads', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('payment_payloads', 'created_at')) {
            Schema::table('payment_payloads', function (Blueprint $table) {
                $table->dropColumn('created_at');
            });
        }

        if (Schema::hasColumn('payment_payloads', 'updated_at')) {
            Schema::table('payment_payloads', function (Blueprint $table) {
                $table->dropColumn('updated_at');
            });
        }
    }
}
