<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.er
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
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('sub_unit_id')->nullable();
            $table->unsignedBigInteger('station_id')->nullable();
            $table->enum('role', ['super_admin', 'regional_police_officer', 'provincial_police_officer', 'municipal_police_officer']);
            $table->string('password');
            $table->rememberToken();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->boolean('is_intel')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('station_id')
                ->references('id')
                ->on('stations');

            $table->foreign('sub_unit_id')
                ->references('id')
                ->on('sub_units');

            $table->foreign('unit_id')
                ->references('id')
                ->on('units');
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
