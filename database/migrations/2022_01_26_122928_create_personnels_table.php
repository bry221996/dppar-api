<?php

use App\Enums\GenderType;
use App\Enums\PersonnelCategory;
use App\Enums\PersonnelClassification;
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
            $table->string('mobile_number')->unique();
            $table->string('email')->unique();
            $table->string('mpin')->nullable();
            $table->string('image')->default('https://dppar.s3.ap-southeast-1.amazonaws.com/personnels/images/default.jpeg');
            $table->string('title');
            $table->string('qualifier')->nullable();
            $table->string('badge_no');
            $table->string('designation');
            $table->enum('category', PersonnelCategory::getAll());
            $table->unsignedBigInteger('classification_id');
            $table->enum('gender', GenderType::getAll());
            $table->timestamp('pin_updated_at')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('classification_id')
                ->references('id')
                ->on('classifications');
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
