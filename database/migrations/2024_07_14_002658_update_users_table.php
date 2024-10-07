<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'niche')) {
                $table->string('niche')->nullable();
            }
            if (!Schema::hasColumn('users', 'cpf')) {
                $table->string('cpf')->nullable()->unique();
            }
            if (!Schema::hasColumn('users', 'discount')) {
                $table->string('discount')->nullable();
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }

            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique();
            }

            if (Schema::hasColumn('users', 'name')) {
                $table->string('name')->unique()->change();
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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'credit')) {
                $table->float('credit')->default(0);
            }

            if (Schema::hasColumn('users', 'referral_code')) {
                $table->dropColumn('referral_code');
            }
            if (Schema::hasColumn('users', 'instagram')) {
                $table->dropColumn('instagram');
            }
            if (Schema::hasColumn('users', 'niche')) {
                $table->dropColumn('niche');
            }
            if (Schema::hasColumn('users', 'cpf')) {
                $table->dropColumn('cpf');
            }
            if (Schema::hasColumn('users', 'discount')) {
                $table->dropColumn('discount');
            }
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('users', 'identity_verified_at')) {
                $table->dropColumn('identity_verified_at');
            }
            if (Schema::hasColumn('users', 'paid_profile')) {
                $table->dropColumn('paid_profile');
            }
            if (Schema::hasColumn('users', 'open_profile')) {
                $table->dropColumn('open_profile');
            }

            if (Schema::hasColumn('users', 'name')) {
                $table->string('name')->unique()->change();
            }
        });
    }
}
