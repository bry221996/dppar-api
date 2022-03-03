<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('type');
            $table->string('classification');
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('sub_unit_id')->nullable();
            $table->unsignedBigInteger('station_id')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('unit_id')
                ->references('id')
                ->on('units');

            $table->foreign('sub_unit_id')
                ->references('id')
                ->on('sub_units');


            $table->foreign('station_id')
                ->references('id')
                ->on('stations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offices');
    }
}
