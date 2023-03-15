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
            $table->bigInteger('adm1_id')->nullable();
            $table->foreign('adm1_id')->references('id')->on('administrative_divisions');
            $table->bigInteger('adm2_id')->nullable();
            $table->foreign('adm2_id')->references('id')->on('administrative_divisions');
            $table->bigInteger('adm3_id')->nullable();
            $table->foreign('adm3_id')->references('id')->on('administrative_divisions');
            $table->text('full_address')->nullable();
            $table->point('coordinates')->nullable();
            $table->index('country');
            $table->index('full_address');
            $table->index('coordinates');

            $table->check(DB::raw('(adm_1 IS NULL OR (SELECT level FROM administrative_division WHERE id = adm_1) = 1)'));
            $table->check(DB::raw('(adm_2 IS NULL OR (SELECT level FROM administrative_division WHERE id = adm_2) = 2)'));
            $table->check(DB::raw('(adm_3 IS NULL OR (SELECT level FROM administrative_division WHERE id = adm_3) = 3)'));
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
