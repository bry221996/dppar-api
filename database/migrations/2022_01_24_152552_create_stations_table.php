<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_unit_id');
            $table->string('name');
            $table->string('municipality');
            $table->decimal('latitude');
            $table->decimal('longitude');
            $table->boolean('is_intel')->default(false);
            $table->boolean('is_mobile_force')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sub_unit_id')
                ->references('id')
                ->on('sub_units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stations');
    }
}
