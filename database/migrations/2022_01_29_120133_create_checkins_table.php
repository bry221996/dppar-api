<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('personnel_id');
            $table->string('image')->nullable();
            $table->string('type');
            $table->string('sub_type')->nullable();
            $table->boolean('is_accounted')->default(true);
            $table->float('latitude', 12, 8)->nullable();
            $table->float('longitude', 12, 8)->nullable();
            $table->string('town')->nullable();
            $table->string('province')->nullable();
            $table->text('remarks')->nullable();
            $table->text('admin_remarks')->nullable();
            $table->json('address_component')->nullable();
            $table->unsignedBigInteger('tagged_as_absent_by')->nullable();
            $table->timestamp('tagged_as_absent_at')->nullable();
            $table->timestamps();

            $table->foreign('personnel_id')
                ->references('id')
                ->on('personnels');

            $table->foreign('tagged_as_absent_by')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('checkins');
    }
}
