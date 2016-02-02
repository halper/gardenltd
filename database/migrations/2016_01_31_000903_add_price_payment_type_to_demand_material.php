<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPricePaymentTypeToDemandMaterial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('demand_material', function (Blueprint $table) {
            //
            $table->double('price')->unsigned()->nullable();;
            $table->string('payment_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('demand_material', function (Blueprint $table) {
            //
            $table->dropColumn('price');
            $table->dropColumn('payment_type');
        });
    }
}
