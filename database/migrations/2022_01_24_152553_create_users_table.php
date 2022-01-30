<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->unsignedBigInteger('regional_police_office_id')->nullable();
            $table->unsignedBigInteger('provincial_police_office_id')->nullable();
            $table->unsignedBigInteger('municipal_police_station_id')->nullable();
            $table->enum('role', ['super_admin', 'regional_police_officer', 'provincial_police_officer', 'municipal_police_officer']);
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('municipal_police_station_id')
                ->references('id')
                ->on('municipal_police_stations');

            $table->foreign('provincial_police_office_id')
                ->references('id')
                ->on('provincial_police_offices');

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
        Schema::dropIfExists('users');
    }
}
