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
     * @param int $offset
     * @return string
     * Соединяет массив
     */
    public static function Join($array, $separator = ',', $offset = 0)
    {
        if (is_array($array)) {
            $array = array_slice($array, $offset);
            $result = implode($separator, $array);
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

    public static function GoId($id)
    {
        return ltrim(ltrim($id, 2), '0');
    }
}