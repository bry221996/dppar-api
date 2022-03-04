<?php

use App\Enums\PersonnelClassification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateClassificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classifications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        DB::table('classifications')->insert([
            [
                'id' => PersonnelClassification::REGULAR,
                'name' => 'regular',
            ],
            [
                'id' => PersonnelClassification::FLEXIBLE_TIME,
                'name' => 'flexible_time',
            ],
            [
                'id' => PersonnelClassification::SOCIAL_UNIT,
                'name' => 'social_unit',
            ],
            [
                'id' => PersonnelClassification::MOBILE_FORCE,
                'name' => 'mobile_force'
            ],
            [
                'id' => PersonnelClassification::UNIT_HEAD,
                'name' => 'unit_head'
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('classifications');
    }
}
