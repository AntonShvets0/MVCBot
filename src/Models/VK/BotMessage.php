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
     * @param int $messageID
     * @param string $text
     * @param array $attach
     * @param string|int $peerID
     * @return bool
     * Редактирует сообщение
     */
    public static function Edit($messageID, $text, $attach = [], $peerID = 'callback')
    {
        if ($peerID == 'callback') {
            $peerID = BotGet::Peer();
        }

        return self::API('messages.edit', ['peer_id' => $peerID, 'message' => $text, 'message_id' => $messageID, 'attachment' => Utils::Join($attach)]);
    }

    /**
     * @param bool $bool
     * @param string|int $peerID
     * @return bool
     * Статус набора текста
     */
    public static function Activity($bool = true, $peerID = 'callback')
    {
        if ($peerID == 'callback') {
            $peerID = BotGet::GetID();
        }

        $type = $bool ? 'typing' : 'audiomessage';
        $data = self::API('messages.setActivity', ['peer_id' => $peerID, 'type' => $type]);

        return $data;
    }

    /**
     * @param int|array $messageID
     * @return bool
     * Удаляет сообщение
     */
    public static function Delete($messageID)
    {
        $messageID = Utils::Join($messageID);

        $data = self::API('messages.delete', ['message_ids' => $messageID, 'delete_for_all' => 1]);
        $bool = false;

        foreach ($data as $val) {
            if ($val == 0) {
                $bool = false;
            } else {
                $bool = true;
            }
        }

        return $bool;
    }

    /**
     * @param string $message
     * @param string|int $id
     * @param array|string $attach
     * @param array $keyBoard
     * @return bool
     * Отправляет сообщение
     */
    public static function Send($message, $id = 'callback', $attach = [], $keyBoard = [])
    {
        if ($id == 'callback') {
            $id = BotGet::GetID();
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

        $data = self::API('messages.send', $data);
        return $data ? true : false;
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
            $result[] = ['action' => ['label' => $name, 'payload' => json_encode($data[1])], 'color' => $data[0]];
        }

        return $result;
    }
}