<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('list_tag', function (Blueprint $table) {
            $table->id();
            $table->foreign('list_id')->references('id')->on('lists');
            $table->bigInteger('tag_id')->primary();
            $table->foreign('tag_id')->references('id')->on('tags');
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
        Schema::dropIfExists('list_tag');
    }
}
