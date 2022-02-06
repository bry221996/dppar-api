<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePersonnelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('personnels', function (Blueprint $table) {
        //     $table->string('image')->default('https://dppar.s3.ap-southeast-1.amazonaws.com/personnels/images/default.jpeg');
        //     $table->string('mpin')->nullable()->change();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('personnels', function (Blueprint $table) {
        //     $table->dropColumn('image');
        //     $table->string('mpin')->change();
        // });
    }
}
