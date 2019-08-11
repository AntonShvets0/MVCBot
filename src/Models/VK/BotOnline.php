<?php

/**
 * @class BotOnline
 * Нужен для работы с онлайном группы
 */

require_once ROOT . '/Models/VK/BotRequest.php';

class BotOnline
{
    /**
     * @return bool
     */
    public static function Online()
    {
        return BotRequest::API('groups.enableOnline', ['group_id' => GROUP]);
    }

    /**
     * @return bool
     * Убирает онлайн у группы
     */
    public static function Offline()
    {
        return BotRequest::API('groups.disableOnline', ['group_id' => GROUP]);
    }

    /**
     * @return array|false
     * Возвращает статистику о онлайне
     */
    public static function Get()
    {
        return BotRequest::API('groups.getOnlineStatus', ['group_id' => GROUP]);
    }
}