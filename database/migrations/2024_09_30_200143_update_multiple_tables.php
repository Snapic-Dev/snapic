<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMultipleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Alterar as colunas na tabela 'users'
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'name')) {
                $table->string('name')->nullable()->change();  // Altera a coluna 'name' existente
            }

            if (Schema::hasColumn('users', 'discount')) {
                $table->string('discount')->default(15)->change();  // Altera a coluna 'discount' existente
            } else {
                $table->string('discount')->default(15);  // Adiciona 'discount' se não existir
            }
        });

        // Alterar ou adicionar colunas na tabela 'transactions'
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'e2eId')) {
                $table->string('e2eId')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'transfer_id')) {
                $table->string('transfer_id')->nullable();
            }
            if (Schema::hasColumn('transactions', 'gift_id')) {
                $table->unsignedBigInteger('gift_id')->nullable()->change();  // Alterar 'gift_id' se existir
            } else {
                $table->unsignedBigInteger('gift_id')->nullable();  // Adicionar 'gift_id' se não existir
            }
        });

        // Alterar ou adicionar colunas na tabela 'posts'
        Schema::table('posts', function (Blueprint $table) {
            if (!Schema::hasColumn('posts', 'requires_subscription')) {
                $table->boolean('requires_subscription')->default(false);
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
        // Desfaz as mudanças feitas nas tabelas
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('users', 'discount')) {
                $table->dropColumn('discount');
            }
        });

        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'e2eId')) {
                $table->dropColumn('e2eId');
            }
            if (Schema::hasColumn('transactions', 'transfer_id')) {
                $table->dropColumn('transfer_id');
            }
            if (Schema::hasColumn('transactions', 'gift_id')) {
                $table->dropColumn('gift_id');
            }
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('requires_subscription');
        });
    }
}
