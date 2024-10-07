<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_verifies', function (Blueprint $table) {
            $table->dropColumn('files'); 
            $table->text('doc_front')->nullable(); 
            $table->text('doc_back')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */

    public function down()
    {
        Schema::table('user_verifies', function (Blueprint $table) {
            $table->text('files')->nullable(); 
            $table->dropColumn('doc_front'); 
            $table->dropColumn('doc_back'); 
        });
    }
};
