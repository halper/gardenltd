<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmdemandSubmaterialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smdemand_submaterial', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('submaterial_id')->index()->nullable();
            $table->foreign('submaterial_id')->references('id')->on('submaterials')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('smdemand_id')->index()->nullable();
            $table->foreign('smdemand_id')->references('id')->on('smdemands')->onDelete('cascade')->onUpdate('cascade');
            $table->double('price', 12, 2);
            $table->string('unit');
            $table->double('quantity', 12, 2);
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
        Schema::drop('smdemand_submaterial');
    }
}
