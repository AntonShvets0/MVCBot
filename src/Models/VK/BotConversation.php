<?php

/**
 * @class BotConversation
 * Нужен для работы с беседами
 */

require_once ROOT . '/Models/VK/BotMessage.php';

class BotConversation
{
    /**
     * @return string
     * Возвращает имя беседы
     */
    public static function GetName()
    {
        $data = self::GetInfo();
        return $data ? $data['chat_settings']['title'] : false;
    }

    /**
     * @return bool
     */
    public static function IsAdmin()
    {
        $data = self::GetAdminsID();
        if (!$data) {
            return false;
        }
        return in_array(-GROUP, $data);
    }


    /**
     * @return int
     * Возвращает id создателя беседы
     */
    public static function GetOwnerID()
    {
        $data = self::GetInfo();
        return $data ? $data['chat_settings']['owner_id'] : false;
    }

    /**
     * @return array|bool
     * Возвращает id пользователей, у которых есть админка
     */
    public static function GetAdminsID()
    {
        $data = self::GetInfo();
        return $data ? $data['chat_settings']['admin_ids'] : false;
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
        $data = BotRequest::API('messages.getConversationsById', ['peer_ids' => $peerID, 'fields' => Utils::Join($fields)]);
        return $data['items'][0] ?? false;
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

        $data = BotRequest::API('messages.deleteChatPhoto', ['chat_id' => $peerID]);
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

        $data = BotRequest::API('messages.editChat', ['chat_id' => Utils::GoId($peerID), 'title' => $title]);

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
        $peerID = Utils::GoId($peerID);

        return BotRequest::API('messages.removeChatUser', ['chat_id' => $peerID, 'member_id' => $user]);
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
        return !BotRequest::API('messages.pin', ['peer_id' => $peerID, 'message_id' => $messageID]) ? false : true;
    }
}