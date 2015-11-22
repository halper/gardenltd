<?php
/**
 * Created by PhpStorm.
 * User: alper
 * Date: 11/13/15
 * Time: 11:22 AM
 */
namespace App\Library {



    class TurkishChar
    {

        public static function tr_up($str)
        {
            $str = str_replace('ı', 'I', $str);
            $str = str_replace('i', 'İ', $str);

            return mb_strtoupper($str, 'utf-8');
        }
    }
}