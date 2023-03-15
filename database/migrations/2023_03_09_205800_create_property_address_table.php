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
            $table->unsignedBigInteger('adm1_id')->nullable();
            $table->foreign('adm1_id')->references('id')->on('administrative_divisions')->where('level', '=', 1);;
            $table->unsignedBigInteger('adm2_id')->nullable();
            $table->foreign('adm2_id')->references('id')->on('administrative_divisions')->where('level', '=', 2);;
            $table->unsignedBigInteger('adm3_id')->nullable();
            $table->foreign('adm3_id')->references('id')->on('administrative_divisions')->where('level', '=', 3);;
            $table->text('full_address')->nullable();
            $table->point('coordinates')->nullable();
            $table->index('country');
            $table->index('full_address');
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
