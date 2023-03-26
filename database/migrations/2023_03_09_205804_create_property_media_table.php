<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('property_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id')->foreign('properties.id');
            $table->enum('type', ['image', 'video', 'blueprint', 'other']);
            $table->tinyInteger('order');
            $table->text('url');
            $table->index(['property_id', 'type']);
        });

        DB::unprepared('
            CREATE TRIGGER set_default_order
            BEFORE INSERT ON property_media
            FOR EACH ROW
            BEGIN
            DECLARE last_order tinyint;

            SELECT COALESCE(MAX(`order`), -1) + 1 INTO last_order
            FROM `property_media`
            WHERE `property_id` = NEW.`property_id` AND `type` = NEW.`type`;

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
        Schema::dropIfExists('property_media');
    }
}
