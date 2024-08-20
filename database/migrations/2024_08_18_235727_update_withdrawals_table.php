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
            $table->string('e2eId')->nullable();
            $table->string('transfer_id')->nullable();
            $table->dropColumn('payment_method');
            $table->dropColumn('stripe_transfer_id');
            $table->dropColumn('stripe_payout_id');
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
            // Reverter as alterações feitas no método up

            // Remover a nova coluna
            $table->dropColumn('new_column');

            // Restaurar a coluna modificada (se necessário)
            $table->decimal('amount', 8, 2)->change();

            // Recriar a coluna removida
            $table->string('old_column')->nullable();

            // Remover o índice
            $table->dropIndex(['user_id']);
        });
    }
}
