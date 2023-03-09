<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListPropertyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('list_property', function (Blueprint $table) {
            $table->id();
            $table->foreign('list_id')->references('id')->on('lists');
            $table->bigInteger('property_id')->primary();
            $table->foreign('property_id')->references('id')->on('properties');
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
        Schema::dropIfExists('list_property');
    }
}
