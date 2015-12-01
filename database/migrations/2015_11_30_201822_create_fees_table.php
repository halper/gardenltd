<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
//    Şantiyeye bağlı taşeronların sabit giderleri
    public function up()
    {
        Schema::create('fees', function (Blueprint $table) {
            $table->increments('id');
            $table->double('breakfast')->nullable();
            $table->double('lunch')->nullable();
            $table->double('supper')->nullable();
            $table->double('material')->nullable();
            $table->double('equipment')->nullable();
            $table->double('oil')->nullable();
            $table->double('cleaning')->nullable();
            $table->double('labour')->nullable();
            $table->double('shelter')->nullable();
            $table->double('sgk')->nullable();
            $table->double('allrisk')->nullable();
            $table->double('isg')->nullable();
            $table->double('contract_tax')->nullable();
            $table->double('kdv')->nullable();
            $table->double('electricity')->nullable();
            $table->double('water')->nullable();
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
        Schema::drop('fees');
    }
}
