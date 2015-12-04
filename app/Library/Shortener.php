<?php
/**
 * Created by PhpStorm.
 * User: alper
 * Date: 04.12.2015
 * Time: 00:23
 */

namespace app\Library;


class Shortener
{

    private static $vowels = ["a", "e", "ı", "i", "o", "ö", "u", "ü"];

    /**
     * Takes a collection and its column and returns an array of shortened strings
     * @param $my_collection
     * @param string $col_name
     * @return array
     */
    public static function shorten_collection($my_collection, $col_name = 'name')
    {
        $shortened_arr = [];
        foreach ($my_collection as $staff) {
            $staff_name = self::shorten($staff->$col_name);
            array_push($shortened_arr, $staff_name);
        }
        return $shortened_arr;
    }

    /**
     * Shortens the given string
     * @param $my_string
     * @return string
     */
    public static function shorten($my_string)
    {
        $pos = strpos($my_string, " ");
        $staff_name = "";

        if ($pos === false) {
            // string needle NOT found in haystack
            $staff_name = $my_string;
        } else {
            // string needle found in haystack
            $staff_words = explode(" ", $my_string);
            $i = 1;
            foreach ($staff_words as $word) {
                if (strlen($word) > 5) {
                    $cut = in_array(mb_substr($word, 3, 1), self::$vowels) ? 3 : 4;
                    $staff_name .= mb_substr($word, 0, $cut, 'utf-8') . ".";
                } else {
                    $staff_name .= $word;
                }
                if ($i < sizeof($staff_words)) {
                    $staff_name .= " ";
                    $i++;
                }
            }
        }
        return $staff_name;
    }
}