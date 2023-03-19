<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdministrativeDivisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('administrative_divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->smallInteger('level');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('parent_id')->on('administrative_divisions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('administrative_divisions');
    }
}
