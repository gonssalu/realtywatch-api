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

        Schema::create('property_addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')->primary();
            $table->foreign('property_id')->references('id')->on('properties');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('adm1_id')->nullable();
            $table->foreign('adm1_id')->references('id')->on('administrative_divisions')->where('level', '=', 1);
            $table->unsignedBigInteger('adm2_id')->nullable();
            $table->foreign('adm2_id')->references('id')->on('administrative_divisions')->where('level', '=', 2);
            $table->unsignedBigInteger('adm3_id')->nullable();
            $table->foreign('adm3_id')->references('id')->on('administrative_divisions')->where('level', '=', 3);
            $table->string('postal_code')->nullable();
            $table->text('full_address')->nullable();
            $table->point('coordinates', '4326')->nullable();
            $table->index('postal_code');
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
