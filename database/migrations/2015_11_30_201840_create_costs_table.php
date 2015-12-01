<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('costs', function (Blueprint $table) {
            $table->increments('id');
            $table->double('breakfast')->nullable();
            $table->double('lunch')->nullable();
            $table->double('supper')->nullable();
            $table->double('material')->nullable();
            $table->double('equipment')->nullable();
            $table->double('oil')->nullable();
            $table->double('cleaning')->nullable();
            $table->double('labour')->nullable();
            $table->date('pay_date');
            $table->text('explanation')->nullable();
            $table->integer('site_id')->unsigned()->index();
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('subcontractor_id')->unsigned()->index();
            $table->foreign('subcontractor_id')->references('id')->on('subcontractors')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::drop('costs');
    }
}
