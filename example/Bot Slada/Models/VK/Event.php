<?php

/**
 * @class Event
 * Регистрирует приходящие типы для CALLBACK API
 */

require_once ROOT . '/Models/Logger.php';

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
        if (isset(self::$event[$type])) {
            Logger::Warning("Event {$type} exists!");
            return false;
        }

        self::$event[$type] = $controller;
        return true;
    }
}