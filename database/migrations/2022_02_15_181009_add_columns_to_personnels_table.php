<?php

use App\Enums\GenderType;
use App\Enums\PersonnelCategory;
use App\Enums\PersonnelClassification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPersonnelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('personnels', function (Blueprint $table) {
            $table->string('title');
            $table->string('qualifier')->nullable();
            $table->string('badge_no');
            $table->string('designation');
            $table->enum('category', PersonnelCategory::getAll());
            $table->enum('classification', PersonnelClassification::getAll());
            $table->enum('gender', GenderType::getAll());
            $table->dropColumn('type');
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
            $table->dropColumn('title');
            $table->dropColumn('qualifier');
            $table->dropColumn('badge_no');
            $table->dropColumn('designation');
            $table->dropColumn('category');
            $table->dropColumn('classification');
            $table->dropColumn('gender');
            $table->enum('type', ['uniformed', 'non_uniformed', 'intel', 'special', 'department_heads']);
        });
    }
}
