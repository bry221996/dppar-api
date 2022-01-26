<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonnelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personnels', function (Blueprint $table) {
            $table->id();
            $table->string('personnel_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name');
            $table->date('birth_date');
            $table->unsignedBigInteger('jurisdiction_id');
            $table->string('mobile_number')->unique();
            $table->string('email')->unique();
            $table->string('mpin');
            $table->enum('type', ['uniformed', 'non_uniformed', 'intel', 'special', 'department_heads']);
            $table->timestamps();

            $table->foreign('jurisdiction_id')
                ->references('id')
                ->on('jurisdictions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personnels');
    }
}
