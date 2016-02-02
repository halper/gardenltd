<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmdexpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smdexpenses', function (Blueprint $table) {
            $table->increments('id');
            $table->double('quantity');
            $table->date('delivery_date');
            $table->string('bill');
            $table->string('detail')->nullable();
            $table->unsignedInteger('submaterial_id')->index()->nullable();
            $table->foreign('submaterial_id')->references('id')->on('submaterials')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('smdemand_id')->index()->nullable();
            $table->foreign('smdemand_id')->references('id')->on('smdemands')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::drop('smdexpenses');
    }
}
