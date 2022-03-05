<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserClassificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_classifications', function (Blueprint $table) {
            if (env('APP_ENV') !== 'testing') {
                \Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
            }

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('classification_id');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->foreign('classification_id')
                ->references('id')
                ->on('classifications');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_classifications');
    }
}
