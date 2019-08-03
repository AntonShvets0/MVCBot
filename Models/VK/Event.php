<?php

/**
 * @class Event
 * Регистрирует приходящие типы для CALLBACK API
 */

class Event
{
    public static $event = [];

    /**
     * @param string|array $type
     * @param mixed $controller
     * @return bool
     */
    public static function Register($type, $controller)
    {
        return true;
    }
}