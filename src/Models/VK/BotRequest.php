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
     * @return array|false
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

        if (self::UseCurl()) {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_POST, true);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_HEADER, false); // Убираем из результата заголовок HTTP

            curl_setopt($ch, CURLOPT_URL, $url); // Ссылка
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // POST данные

            $response = curl_exec($ch);
            curl_close($ch);
        } else { // Если cURL не установлен, то выполняем запрос через file_get_contents
            $header = ['http' =>
                [
                    'method'  => 'POST',
                    'header'  => 'Content-Type: application/x-www-form-urlencoded',
                    'content' => $data
                ]
            ];

            $response = file_get_contents($url, false, stream_context_create($header));
        }

        return $response;
    }

    /**
     * @return bool
     * Проверка на cURL
     */
    private static function UseCurl()
    {
        return extension_loaded('curl');
    }
}