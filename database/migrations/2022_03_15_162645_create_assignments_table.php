<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('personnel_id');
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('sub_unit_id')->nullable();
            $table->unsignedBigInteger('station_id')->nullable();
            $table->timestamps();

            $table->foreign('personnel_id')
                ->references('id')
                ->on('personnels');

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
        Schema::dropIfExists('assignments');
    }
}
