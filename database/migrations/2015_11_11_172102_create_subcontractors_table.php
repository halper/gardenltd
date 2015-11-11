<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubcontractorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcontractors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->date('contract_date');
            $table->date('contract_start_date');
            $table->date('contract_end_date');
            $table->integer('manufacturing_id')->unsigned()->index();
            $table->foreign('manufacturing_id')->references('id')->on('manufacturings')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::drop('subcontractors');
    }
}
