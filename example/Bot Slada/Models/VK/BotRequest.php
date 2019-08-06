<?php

/**
 * @class BotRequest
 * Класс нужен для других классов, которые взаимодействуют с ботом
 * Отправляет запросы
 */

require_once ROOT . '/Models/Logger.php';


class BotRequest
{
    /**
     * @param string $method
     * @param array $data
     * @return array|string|int|false
     * Отправляет запрос на VK API
     */
    public static function API($method, $data = [])
    {
        $data = array_merge($data, CONFIG['vkSendData']);

        Logger::Info("Send request https://api.vk.com/method/{$method}, data: " . json_encode($data));

        $response = json_decode(self::SendPost('https://api.vk.com/method/' . $method, $data), true);

        if (isset($response['error'])) {
            Logger::Error("Response error(#{$response['error']['error_code']}): {$response['error']['error_msg']}");
            return false;
        }

        return $response['response'];
    }

    /**
     * @param string $url
     * @param array|string $data
     * @return false|string
     * Отправляет POST запрос на URL, и возвращает результат
     */
    protected static function SendPost($url, $data = [])
    {
        if (!is_string($data)) {
            $data = http_build_query($data);
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HEADER, false); // Убираем из результата заголовок HTTP

        curl_setopt($ch, CURLOPT_URL, $url); // Ссылка
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // POST данные

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public static function SendPostFile($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data; charset=UTF-8']);

        $result = curl_exec($ch);

        curl_close($ch);

        return json_decode($result, true);
    }
}