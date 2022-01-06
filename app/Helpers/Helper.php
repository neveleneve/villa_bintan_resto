<?php

namespace App\Helpers;

class Helper
{
    public static function RandomChar($char, $length = 10)
    {
        $charactersLength = strlen($char);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $char[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
