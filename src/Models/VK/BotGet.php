<?php

/**
 * @class BotGet
 * Нужен для получения информации
 */

require_once ROOT . '/Models/Utils.php';
require_once ROOT . '/Models/VK/BotRequest.php';

class BotGet
{
    /**
     * @return bool
     * Если это беседа, то возвратит True
     */
    public static function IsConversation()
    {
        return BotGet::From() != BotGet::Peer();
    }

    /**
     * @return bool
     * Возвратит FROM-id
     */
    public static function From()
    {
        return Utils::IfExistsReturn('from_id');
    }

    /**
     * @return bool
     * Возвратит PEER-id
     */
    public static function Peer()
    {
        return Utils::IfExistsReturn('peer_id');
    }

    /**
     * @return bool
     * Возвратит USER-id
     */
    public static function User()
    {
        return Utils::IfExistsReturn('user_id');
    }

    /**
     * @return bool
     * Возвращает ID
     */
    public static function GetID()
    {
        return self::Peer() ? self::Peer() :
            (self::From() ? self::From() : self::User());
    }

    /**
     * @return string
     * Возвращает сообщение
     */
    public static function Message()
    {
        if (self::HasPayLoad()) {
            return Utils::Join(json_decode(VK['payload'], true), ' ');
        }
        return VK['text'];
    }

    /**
     * @return bool
     * Имеет ли запрос Payload
     */
    public static function HasPayLoad()
    {
        return isset(VK['payload']);
    }

    /**
     * @param string $id
     * @return int
     * Возвращает gender пользователя
     */
    public static function Gender($id = 'callback')
    {
        $data = self::Info($id, 'sex')[0]['sex'];

        return $data - 1;
    }

    /**
     * @param bool $lastName
     * @param string $nameCase
     * @param string $id
     * @return string
     * Возвращает имя
     */
    public static function Name($lastName = true, $nameCase = 'nom', $id = 'callback')
    {
        $data = self::Info($id, [], $nameCase)[0];
        return $lastName ? $data['first_name'] . ' ' . $data['last_name'] : $data['first_name'];
    }

    /**
     * @param string $ids
     * @param array $fields
     * @param string $nameCase
     * @return array|false|int|string
     * Возвращает информацию о пользователе
     */
    public static function Info($ids = 'callback', $fields = [], $nameCase = 'nom')
    {
        if (is_string($ids) && $ids == 'callback') {
            $ids = self::From();
        } elseif (is_array($ids)) {
            $ids = Utils::Join($fields);
        }
        $fields = Utils::Join($fields);
        return BotRequest::API('users.get', ['user_ids' => $ids, 'fields' => $fields, 'name_case' => $nameCase]);
    }
}