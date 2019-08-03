<?php

/**
 * @class Logger
 * Класс записывает информацию в логи
 */

class Logger
{
    /**
     * @param string $message
     * @param string $type
     * @return void
     * Записывает лог в файл
     */
    public static function Add($message, $type = 'Default')
    {
        if (DEBUG) {
            $data = date('H:i:s');
            file_put_contents(self::GetFile(), "[$data] [{$type}]: $message" . PHP_EOL, FILE_APPEND);
        }
    }

    /**
     * @param string $type
     * @return string
     * Определяет файл для записи
     */
    private static function GetFile()
    {
        return ROOT . '/Log/' . date('d_m_y') . '.txt';
    }
}