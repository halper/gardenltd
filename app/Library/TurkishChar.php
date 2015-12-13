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
            return mb_strtoupper(TurkishChar::convertI($str), 'utf-8');
        }

        public static function tr_camel($str)
        {
            return mb_convert_case(self::convertFirst($str), MB_CASE_TITLE, 'utf-8');
        }

        private static function convertFirst($str)
        {
            $str_arr = explode(" ", $str);
            $my_str = "";
            foreach($str_arr as $word){
                $my_str .= substr_replace($word, self::convertI(substr($word, 0, 1)), 0, 1) . " ";
            }
            return $my_str;
        }

        private static function convertI($str)
        {
            return str_replace('i', 'İ', str_replace('ı', 'I', $str));
        }

    }
}