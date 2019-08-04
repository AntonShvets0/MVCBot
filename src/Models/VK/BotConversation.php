<?php

/**
 * @class BotConversation
 * Нужен для работы с беседами
 */

require_once ROOT . '/Models/VK/BotMessage.php';

class BotConversation extends BotMessage
{
    /**
     * @param string $peerID
     * @return bool
     * Удаляет из беседы фото
     */
    public static function DeletePhoto($peerID = 'callback')
    {
        if ($peerID == 'callback') {
            $peerID = BotGet::Peer();
        }

        $data = self::API('messages.deleteChatPhoto', ['chat_id' => $peerID]);
        return !$data ? false : true;
    }

    /**
     * @param $title
     * @param string $peerID
     * @return bool
     * Редактирует заголовок беседы
     */
    public static function Title($title, $peerID = 'callback')
    {
        if ($peerID == 'callback') {
            $peerID = BotGet::Peer();
        }

        $data = self::API('messages.editChat', ['chat_id' => $peerID, 'title' => $title]);

        return $data;
    }

    /**
     * @param int $user
     * @param string|int $peerID
     * @return bool
     */
    public static function DeleteUser($user, $peerID = 'callback')
    {
        if ($peerID == 'callback') {
            $peerID = BotGet::Peer();
        }

        return self::API('messages.removeChatUser', ['chat_id' => $peerID, 'member_id' => $user]);
    }

    /**
     * @param int $messageID
     * @param string $peerID
     * @return bool
     * Закрепляет сообщение
     */
    public static function Pin($messageID, $peerID = 'callback')
    {
        if ($peerID == 'callback') {
            $peerID = BotGet::Peer();
        }
        return !self::API('messages.pin', ['peer_id' => $peerID, 'message_id' => $messageID]) ? false : true;
    }
}