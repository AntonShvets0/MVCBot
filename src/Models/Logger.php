<?php

/**
 * @class Logger
 * Класс записывает информацию в логи
 */

class Logger
{
    /**
     * @param string $message
     */
    public static function Error($message)
    {
        self::Add($message, 'Error');
    }

    /**
     * @param string $message
     */
    public static function Warning($message)
    {
        self::Add($message, 'Warning');
    }

    /**
     * @param string $message
     */
    public static function Info($message)
    {
        self::Add($message, 'Default');
    }

    /**
     * @param string $message
     * @param string $type
     * @return void
     * Записывает лог в файл
     */
    private static function Add($message, $type = 'Default')
    {
        if (DEBUG) {
            $data = date('H:i:s');
            $file = fopen(self::GetFile(), 'a+');
            fwrite($file, "[{$data}] [{$type}]: {$message}" . PHP_EOL);
            fclose($file);
        }
    }

    /**
     * @return string
     * Определяет файл для записи
     */
    private static function GetFile()
    {
        return ROOT . '/Log/' . date('d_m_y') . '.txt';
    }
}