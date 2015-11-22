<?php
/**
 * Created by PhpStorm.
 * User: alper
 * Date: 11/13/15
 * Time: 11:22 AM
 */
namespace App\Library {

    use Carbon\Carbon;

    class CarbonHelper
    {

        public static function getMySQLDate($turkish_date)
        {
            return Carbon::parse($turkish_date)->toDateString();
        }

        public static function getTurkishDate($mysql_date)
        {
            return Carbon::parse($mysql_date)->format('d.m.Y');
        }
    }
}