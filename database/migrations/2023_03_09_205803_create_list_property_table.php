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
            $table->unsignedBigInteger('list_id');
            $table->foreign('list_id')->references('id')->on('lists');
            $table->unsignedBigInteger('property_id');
            $table->integer('order');
            $table->foreign('property_id')->references('id')->on('properties');
            $table->primary(['list_id', 'property_id']);
        });

        DB::unprepared('
                CREATE TRIGGER list_property_set_default_order
                BEFORE INSERT ON list_property
                FOR EACH ROW
                BEGIN
                DECLARE last_order tinyint;

                SELECT COALESCE(MAX(`order`), -1) + 1 INTO last_order
                FROM `list_property`
                WHERE `list_id` = NEW.`list_id`;

                SET NEW.`order` = last_order;
                END;
            ');

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
