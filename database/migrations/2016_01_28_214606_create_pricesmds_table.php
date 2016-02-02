<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricesmdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pricesmds', function (Blueprint $table) {
            $table->increments('id');
            $table->double('price', 12,2)->unsigned();
            $table->date('since')->default('1970-1-1');
            $table->unsignedInteger('submaterial_id')->index();
            $table->foreign('submaterial_id')->references('id')->on('submaterials')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('smdemand_id')->index();
            $table->foreign('smdemand_id')->references('id')->on('smdemands')->onDelete('cascade')->onUpdate('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pricesmds');
    }
}
