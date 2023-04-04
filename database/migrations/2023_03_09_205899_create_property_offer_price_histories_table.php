<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyOfferPriceHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('property_offer_price_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('offer_id');
            $table->foreign('offer_id')->references('id')->on('property_offers');
            $table->dateTime('datetime');
            $table->decimal('price', 11, 2)->nullable();
            $table->boolean('latest');
            $table->primary(['offer_id', 'datetime']);
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
        Schema::dropIfExists('property_offer_price_histories');
    }
}
