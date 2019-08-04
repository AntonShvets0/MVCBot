<?php

/**
 * @class BotMessage
 * Нужен для отправки сообщений
 */

require_once ROOT . '/Models/VK/BotRequest.php';
require_once ROOT . '/Models/VK/BotGet.php';
require_once ROOT . '/Models/Utils.php';

class BotMessage extends BotRequest
{
    /**
     * @param string $message
     * @param string|int $id
     * @param array $attach
     * @param array $keyBoard
     * @return void
     * Отправляет сообщение
     */
    public static function Send($message, $id = 'callback', $attach = [], $keyBoard = [])
    {
        if ($id == 'callback') {
            $id = BotGet::Peer();
        }

        $keyBoard = self::CreateKeyBoard($keyBoard);

        $data = [
            'random_id' => mt_rand(0, 1000),
            'peer_id' => $id,
            'message' => $message,
            'attachment' => Utils::Join($attach),
            'keyboard' => $keyBoard
        ];

        Logger::Info("Message \"{$message}\" to {$id}");

        self::API('messages.send', $data);
    }

    /**
     * @param array $keyBoard
     * @param bool $vkStyle
     * @return string
     * Создает JSON-клавиатуру для VK
     */
    private static function CreateKeyBoard($keyBoard, $vkStyle = false)
    {
        if ($vkStyle) {
            return json_encode($vkStyle, true);
        }

        $json = [
            'one_time' => array_shift($keyBoard),
            'buttons' => [

            ]
        ];

        foreach ($keyBoard as $item) {
            $json['buttons'][] = self::CreateKeyBoardRows($item);
        }


        return json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param $array
     * @return array
     */
    private static function CreateKeyBoardRows($array)
    {
        $result = [];

        foreach ($array as $name => $data) {
            $result[] = ['action' => ['label' => $name, 'payload' => json_encode($data['cmd']), 'type' => 'text'], 'color' => $data['clr']];
        }

        return $result;
    }
}