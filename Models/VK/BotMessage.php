<?php

/**
 * @class BotMessage
 * Нужен для отправки сообщений
 */

require_once ROOT . '/Models/VK/BotRequest.php';

class BotMessage extends BotRequest
{
    /**
     * @param string $message
     * @param string|int $id
     * @param array $attach
     * @param array $keyBoard
     * Отправляет сообщение
     */
    public static function Send($message, $id = 'callback', $attach = [], $keyBoard = [])
    {

    }

    /**
     * @param array $keyBoard
     * @param bool $vkStyle
     * Создает JSON-клавиатуру для VK
     */
    private static function CreateKeyBoard($keyBoard, $vkStyle = false)
    {

    }
}