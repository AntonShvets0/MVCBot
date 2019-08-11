<?php

/**
 * @class BotMessage
 * Нужен для отправки сообщений
 */

require_once ROOT . '/Models/VK/BotRequest.php';
require_once ROOT . '/Models/VK/BotGet.php';
require_once ROOT . '/Models/Utils.php';

class BotMessage
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

        return BotRequest::API('messages.edit', ['peer_id' => $peerID, 'message' => $text, 'message_id' => $messageID, 'attachment' => Utils::Join($attach)]);
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
        $data = BotRequest::API('messages.setActivity', ['peer_id' => $peerID, 'type' => $type]);

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

        $data = BotRequest::API('messages.delete', ['message_ids' => $messageID, 'delete_for_all' => 1]);
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
     * @param string $keyBoard
     * @return bool
     * Отправляет сообщение
     */
    public static function Send($message, $id = 'callback', $attach = [], $keyBoard = "")
    {
        if ($id == 'callback') {
            $id = BotGet::GetID();
        }

        $message = str_replace(['<br />', '<br>'], PHP_EOL, $message);

        $count = mb_strlen($message) / 4000; // Ограничение в вк по кол-ву символов на сообщение -- 4000
        $count = $count > 1 ? $count : 1;

        $data = false;

        for ($i = 0; $i < $count; $i++) {
            $msg = mb_substr($message, $i * 4000, 4000);

            $isLastI = $i >= $count - 1;

            $data = [
                'random_id' => mt_rand(0, 1000),
                'peer_id' => $id,
                'message' => $msg,
            ];

            if ($isLastI) {
                $data['attachment'] = Utils::Join($attach);
                $data['keyboard'] = $keyBoard;
            }

            $data = BotRequest::API('messages.send', $data);
        }

        Logger::Info("Message \"{$message}\" to {$id}", 2);

        return $data ? true : false;
    }
}