<?php

class StringHelper
{

    public static function highlightSearchString($haystack, $needle, $tag = 'span')
    {
        $result = $haystack;

        if ($needle) {
            $pos = mb_stripos($haystack, $needle);

            if ($pos !== false) {
                $tag_open = '<'.$tag.'>';
                $tag_close = '</'.$tag.'>';

                $result = mb_substr($haystack, 0, $pos).$tag_open.mb_substr($haystack, $pos,
                        mb_strlen($needle)).$tag_close.mb_substr($haystack, ($pos + mb_strlen($needle)));

                // $result = substr_replace($haystack, $tag_open, $pos, 0);
                // $result = substr_replace($haystack, $tag_close, $pos + strlen($tag_open), 0);
            }
        }

        return $result;
    }

    /**
     * Мультибайтовый перевод первого символа строки в верхний регистр
     *
     * @param $string
     * @return string
     */
    public static function mbUcFirst($string)
    {
        $firstSymbol = mb_strtoupper(mb_substr($string, 0, 1));

        return $firstSymbol.mb_substr($string, 1);
    }
}