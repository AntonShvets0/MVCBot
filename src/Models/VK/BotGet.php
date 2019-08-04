<?php
require_once ROOT . '/Models/Utils.php';

class BotGet
{
    public static function From()
    {
        return VK['from_id'];
    }

    public static function Peer()
    {
        return VK['peer_id'];
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