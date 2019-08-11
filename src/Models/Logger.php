<?php

/**
 * @class Logger
 * Класс записывает информацию в логи
 */

class Logger
{
    /**
     * @param string $message
     * @param int $level
     * @param bool $error
     */
    public static function Error($message, $level = 1, $error = false)
    {
        self::Add($message, 'Error', $level, $error);
    }

    /**
     * @param $message
     * @param int $level
     * @param bool $error
     */
    public static function Warning($message, $level = 1, $error = false)
    {
        self::Add($message, 'Warning', $level, $error);
    }

    /**
     * @param $message
     * @param int $level
     * @param bool $error
     */
    public static function Info($message, $level = 1, $error = false)
    {
        self::Add($message, 'Default', $level, $error);
    }

    /**
     * @param $message
     * @param string $type
     * @param int $level
     * @param bool $error
     * Записывает лог в файл
     */
    private static function Add($message, $type = 'Default', $level = 1, $error = false)
    {
        if (LOGGER_LEVEL >= $level) {
            $data = date('H:i:s');
            $file = fopen(self::GetFile($error), 'a+');
            fwrite($file, "[{$data}] [{$type}]: {$message}" . PHP_EOL);
            fclose($file);
        }
    }

    /**
     * @param bool $error
     * @return string
     * Определяет файл для записи
     */
    private static function GetFile($error = false)
    {
        return !$error ? ROOT . '/Log/' . date('d_m_y') . '.txt' : ROOT . '/Log/' . date('d_m_y') . '_err.txt';
    }
}