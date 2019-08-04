<?php

/**
 * @class Utils
 * Всякие полезные утилиты
 */

class Utils
{
    /**
     * @param $index
     * @param bool $return
     * @return bool
     */
    public static function IfExistsReturn($index, $return = false)
    {
        return isset(VK[$index]) ? VK[$index] : $return;
    }

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

    /**
     * @param $numberOf
     * @param $value
     * @param $suffix
     * @return string
     */
    public static function Num2Str($numberOf, $value, $suffix)
    {
        $keys = [2, 0, 1, 1, 1, 2];
        $mod = $numberOf % 100;
        $suffix_key = $mod > 4 && $mod < 20 ? 2 : $keys[min($mod%10, 5)];

        return $value . $suffix[$suffix_key];
    }
}