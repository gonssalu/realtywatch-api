<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('property_address', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')->primary();
            $table->foreign('property_id')->references('id')->on('properties');
            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->string('locality')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('street')->nullable();
            $table->point('coordinates')->nullable();
            $table->index('country');
            $table->index('region');
            $table->index('locality');
            $table->index('postal_code');
            $table->index('street');
            $table->index('coordinates');
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
        Schema::dropIfExists('property_address');
    }
}
