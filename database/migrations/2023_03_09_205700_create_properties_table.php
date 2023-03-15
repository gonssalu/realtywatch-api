<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->smallInteger('quantity')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('cover_url')->nullable();
            $table->decimal('gross_area')->nullable();
            $table->decimal('useful_area')->nullable();
            $table->string('type')->nullable();
            $table->string('typology')->nullable();
            $table->tinyInteger('rating')->nullable();
            $table->decimal('current_price', 9, 2)->nullable();
            $table->enum('status', ['available', 'sold', 'rented', 'unavailable', 'unknown'])->default('unknown');
            $table->index('title');
            $table->index('description');
            $table->index('status');
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
        Schema::dropIfExists('properties');
    }
}
