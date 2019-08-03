<?php


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
        return VK['text'];
    }
}