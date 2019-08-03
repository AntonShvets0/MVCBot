<?php

/**
 * @class BotRequest
 * Класс нужен для других классов, которые взаимодействуют с ботом
 * Отправляет запросы
 */

class BotRequest
{
    /**
     * @param string $method
     * @param array $data
     * @return array
     * Отправляет запрос на VK API
     */
    public function On($method, $data = [])
    {
        return [];
    }

    /**
     * @param string $url
     * @param array|string $data
     * @return false|string
     * Отправляет POST запрос на URL, и возвращает результат
     */
    protected function SendPost($url, $data = [])
    {
        return false;
    }

    /**
     * @return bool
     * Проверка на cURL
     */
    private function UseCurl()
    {
        return extension_loaded('curl');
    }
}