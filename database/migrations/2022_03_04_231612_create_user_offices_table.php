<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_offices', function (Blueprint $table) {
            if (env('APP_ENV') !== 'testing') {
                \Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
            }

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('office_id');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->foreign('office_id')
                ->references('id')
                ->on('offices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_offices');
    }
}
