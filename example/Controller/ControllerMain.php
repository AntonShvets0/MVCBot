<?php


class ControllerMain
{
    function ActionRandom($min, $max)
    {
        $random = $this->GetArrayRand($min, $max);
        return "ВАШЕ СЛУЧАЙНОЕ ЧИСЛО: {$this->Mean($random)}";
    }

    /**
     * @param int $min
     * @param int $max
     * @return array
     */
    function GetArrayRand($min, $max)
    {
        $array = [];
        for ($i = 0; $i < 250; $i++) {
            $array[] = mt_rand($min, $max);
        }
        return $array;
    }

    /**
     * @param array $array
     * @return float|int
     * Среднее арифметическое
     */
    function Mean($array)
    {
        return array_sum($array) / count($array);
    }
}