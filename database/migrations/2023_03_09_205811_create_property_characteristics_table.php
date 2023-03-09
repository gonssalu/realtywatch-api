<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyCharacteristicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('property_characteristics', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('property_id');
            $table->foreign('property_id')->references('id')->on('properties');
            $table->string('name');
            $table->string('value');
            $table->unique(['property_id', 'name']);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('property_characteristics');
    }
}
