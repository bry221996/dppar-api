<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveJurisdictionIdInPersonnelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('personnels', function (Blueprint $table) {
            $table->dropForeign('personnels_jurisdiction_id_foreign');
            $table->dropColumn('jurisdiction_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('personnels', function (Blueprint $table) {
            $table->unsignedBigInteger('jurisdiction_id');

            $table->foreign('jurisdiction_id')
                ->references('id')
                ->on('jurisdictions');
        });
    }
}
