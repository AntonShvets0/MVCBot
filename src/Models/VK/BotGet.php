<?php

/**
 * @class BotGet
 * Нужен для получения информации
 */

require_once ROOT . '/Models/Utils.php';

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

    public static function isPayLoad()
    {
        return isset(VK['payload']);
    }
}