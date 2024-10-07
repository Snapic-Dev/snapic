<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('withdrawals', function (Blueprint $table) {

            if (!Schema::hasColumn('withdrawals', 'e2eId')) {
                $table->string('e2eId')->nullable();
            }


            if (!Schema::hasColumn('withdrawals', 'transfer_id')) {
                $table->string('transfer_id')->nullable();
            }


            if (Schema::hasColumn('withdrawals', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
            if (Schema::hasColumn('withdrawals', 'stripe_transfer_id')) {
                $table->dropColumn('stripe_transfer_id');
            }
            if (Schema::hasColumn('withdrawals', 'stripe_payout_id')) {
                $table->dropColumn('stripe_payout_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            // Restaurar as colunas removidas
            $table->string('payment_method')->nullable();
            $table->string('stripe_transfer_id')->nullable();
            $table->string('stripe_payout_id')->nullable();
            $table->dropColumn(['e2eId', 'transfer_id']);
        });
    }
}
