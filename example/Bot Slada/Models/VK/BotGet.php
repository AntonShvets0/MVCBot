<?php

/**
 * @class BotGet
 * Нужен для получения информации
 */

require_once ROOT . '/Models/Utils.php';
require_once ROOT . '/Models/VK/BotRequest.php';

class BotGet
{
    public static function From()
    {
        return Utils::IfExistsReturn('from_id');
    }

    public static function Peer()
    {
        return Utils::IfExistsReturn('peer_id');
    }

    public static function User()
    {
        return Utils::IfExistsReturn('user_id');
    }

    public static function GetID()
    {
        return self::Peer() ? self::Peer() :
            (self::From() ? self::From() : self::User());
    }

    public static function Message()
    {
        if (isset(VK['payload'])) {
            return Utils::Join(VK['payload'], ' ');
        }
        return VK['text'];
    }

    public static function HasPayLoad()
    {
        return isset(VK['payload']);
    }

    public static function Gender($id = 'callback')
    {
        $data = self::Info($id, 'sex')[0]['sex'];

        return $data - 1;
    }

    public static function Name($lastName = true, $nameCase = 'nom', $id = 'callback')
    {
        $data = self::Info($id, [], $nameCase)[0];
        return $lastName ? $data['first_name'] . ' ' . $data['last_name'] : $data['first_name'];
    }

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