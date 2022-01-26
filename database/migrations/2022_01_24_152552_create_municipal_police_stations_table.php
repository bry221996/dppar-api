<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMunicipalPoliceStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('municipal_police_stations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provincial_police_office_id');
            $table->string('name');
            $table->decimal('latitude');
            $table->decimal('longitude');
            $table->timestamps();

            $table->foreign('provincial_police_office_id')
                ->references('id')
                ->on('provincial_police_offices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('municipal_police_stations');
    }
}
