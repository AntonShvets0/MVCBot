<?php

/**
 * @class BotConversation
 * Нужен для работы с беседами
 */

require_once ROOT . '/Models/VK/BotMessage.php';

class BotConversation extends BotMessage
{
    /**
     * @return string
     * Возвращает имя беседы
     */
    public static function GetName()
    {
        return self::GetInfo()['chat_settings']['title'];
    }

    /**
     * @return bool
     */
    public static function IsAdmin()
    {
        $data = self::GetInfo();
        return $data != false;
    }


    /**
     * @return int
     * Возвращает id создателя беседы
     */
    public static function GetOwnerID()
    {
        return self::GetInfo()['chat_settings']['owner_id'];
    }

    /**
     * @return array
     * Возвращает id пользователей, у которых есть админка
     */
    public static function GetAdminsID()
    {
        return self::GetInfo()['chat_settings']['admin_ids'];
    }

    /**
     * @param string $peerID
     * @param array $fields
     * @return array|false
     * Возвращает информацию о беседе
     */
    public static function GetInfo($peerID = 'callback', $fields = [])
    {
        if ($peerID == 'callback') {
            $peerID = BotGet::Peer();
        }
        $data = self::API('messages.getConversationsById', ['peer_ids' => $peerID, 'fields' => Utils::Join($fields)]);
        return isset($data[0]) ? $data[0] : false;
    }

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