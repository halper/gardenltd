<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstrumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instruments', function (Blueprint $table) {
            $table->increments('id');
            $table->date('followup_date');
            $table->string('firm')->nullable();
            $table->unsignedInteger('equipment_id')->index();
            $table->foreign('equipment_id')->references('id')->on('equipments')->onDelete('cascade')->onUpdate('cascade');
            $table->string('plate')->nullable();
            $table->unsignedTinyInteger('fuel_stat')->comment = "0 hariç, 1 dahil";
            $table->integer('fuel')->nullable();
            $table->double('work', 6, 2)->nullable();
            $table->string('unit')->nullable();
            $table->double('fee', 12, 2)->nullable();
            $table->double('total', 12, 2)->nullable();
            $table->text('detail')->nullable();
            $table->unsignedInteger('site_id')->index();
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade')->onUpdate('cascade');

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
        Schema::drop('instruments');
    }
}
