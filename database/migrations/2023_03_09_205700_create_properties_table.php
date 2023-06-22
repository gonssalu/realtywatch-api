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
            $table->enum('listing_type', ['sale', 'rent', 'both', 'none']);
            $table->smallInteger('quantity')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('cover_url')->nullable();
            $table->decimal('gross_area')->nullable();
            $table->decimal('useful_area')->nullable();
            $table->enum('type', ['house', 'apartment', 'office', 'shop', 'warehouse', 'garage', 'land', 'other'])->nullable();
            $table->string('typology')->nullable();
            $table->tinyInteger('wc')->nullable();
            $table->tinyInteger('rating')->nullable();
            $table->decimal('current_price_sale', 11, 2)->nullable();
            $table->decimal('current_price_rent', 11, 2)->nullable();
            $table->enum('status', ['available', 'unavailable', 'unknown'])->default('unknown');
            $table->index('title');
            $table->index('description');
            $table->index('status');
            $table->index('listing_type');
            $table->timestamps();
            $table->softDeletes();
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
