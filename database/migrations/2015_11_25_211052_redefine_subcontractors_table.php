<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RedefineSubcontractorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subcontractors', function (Blueprint $table) {
            //
            /*$table->dropForeign('subcontractors_site_id_foreign');
            $table->dropColumn('site_id');
            $table->dropColumn('contract_date');
            $table->dropColumn('contract_start_date');
            $table->dropColumn('contract_end_date');*/
            $table->text('address');
            $table->integer('city_id')->unsigned()->index();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade')->onUpdate('cascade');
            $table->string('official');
            $table->string('title');
            $table->integer('area_code_id')->unsigned()->index();
            $table->foreign('area_code_id')->references('id')->on('area_codes')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('phone');
            $table->integer('fax_code_id')->unsigned()->index();
            $table->foreign('fax_code_id')->references('id')->on('area_codes')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('fax');
            $table->integer('mobile_code_id')->unsigned()->index();
            $table->foreign('mobile_code_id')->references('id')->on('mobile_codes')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('mobile');
            $table->string('email');
            $table->string('web')->nullable();
            $table->string('tax_office')->nullable();
            $table->string('tax_number');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subcontractors', function (Blueprint $table) {
            //
            $table->integer('site_id')->unsigned()->index();
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade')->onUpdate('cascade');
            $table->dateTime('contract_date');
            $table->dateTime('contract_start_date');
            $table->dateTime('contract_end_date');
            $table->dropColumn('address');
            $table->dropForeign('subcontractors_city_id_foreign');
            $table->dropColumn('city_id');
            $table->dropColumn('official');
            $table->dropColumn('title');
            $table->dropForeign('subcontractors_area_code_id_foreign');
            $table->dropColumn('area_code_id');
            $table->dropColumn('phone');
            $table->dropForeign('subcontractors_fax_code_id_foreign');
            $table->dropColumn('fax_code_id');
            $table->dropColumn('fax');
            $table->dropForeign('subcontractors_mobile_code_id_foreign');
            $table->dropColumn('mobile_code_id');
            $table->dropColumn('mobile');
            $table->dropColumn('email');
            $table->dropColumn('web');
            $table->dropColumn('tax_office');
            $table->dropColumn('tax_number');
        });
    }
}