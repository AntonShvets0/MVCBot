<?php

/**
 * @class Utils
 * Всякие полезные утилиты
 */

class Utils
{
    /**
     * @param $array
     * @param string $separator
     * @return string
     * Соединяет массив
     */
    public static function Join($array, $separator = ',')
    {
        $result = "";

        if (is_array($array)) {
            foreach ($array as $item) {
                $result .= $item . $separator;
            }
            $result = mb_substr($result, 0, -mb_strlen($separator));
        } else {
            $result = $array;
        }

        return $result;
    }
}