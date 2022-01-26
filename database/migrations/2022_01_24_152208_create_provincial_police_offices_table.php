<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvincialPoliceOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provincial_police_offices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('regional_police_office_id');
            $table->string('name');
            $table->string('province');
            $table->enum('type', ['provincial', 'city']);
            $table->decimal('latitude');
            $table->decimal('longitude');
            $table->timestamps();

            $table->foreign('regional_police_office_id')
                ->references('id')
                ->on('regional_police_offices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('provincial_police_offices');
    }
}
