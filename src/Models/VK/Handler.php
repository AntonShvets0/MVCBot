<?php

/**
 * @class Handler
 * Этот класс нужен для того, чтобы регистрировать новые функции в боте, удалять их
 */

require_once ROOT . '/Models/VK/BotGet.php';
require_once ROOT . '/Models/VK/BotMessage.php';
require_once ROOT . '/Models/FunctionHandler.php';

class Handler
{
    /**
     * @var bool
     * Использовать ли режим "обращений"
     */
    private static $useAppeal = false;

    /**
     * @var array
     * Список обращений
     */
    private static $appealList = [];

    /**
     * @var bool
     * Строгая проверка обращения, и команд
     */
    private static $strictCheck = false;

    /**
     * @var bool
     * "Игнор режим"
     */
    private static $ignoreOtherMessages = false;

    /**
     * @var string
     * Символ, с которого должно начинаться сообщение (только для Игнор Режима)
     */
    private static $noIgnoreChar = "";

    /**
     * @var array
     * Список функций
     */
    private static $functionList = [];

    /**
     * @param string|array $function
     * @param mixed $controller
     * @return bool
     * Регистрирует новую функцию
     */
    public static function Register($function, $controller)
    {
        if (is_array($function)) {
            array_map(function ($item) use ($controller) {
                self::Register($item, $controller);
            }, $function);
        } else {
            if (!self::$strictCheck) {
                $function = mb_strtolower($function);
            }
            if (isset(self::$functionList[$function])) {
                Logger::Warning("Function {$function} exists");
                return false;
            }
            self::$functionList[$function] = $controller;
        }
        return true;
    }

    /**
     * @param bool $bool
     * @param string $noIgnoreChar
     * @return void
     * Включает/Отключает "игнор мод", когда бот не реагирует на сообщения, если они не начинаются на определенную букву
     */
    public static function IgnoreMode($bool, $noIgnoreChar = "")
    {
        self::$ignoreOtherMessages = $bool;
        self::$noIgnoreChar = $noIgnoreChar;
    }

    /**
     * @param bool $bool
     * Включает/Отключает функцию обращений. Бот не будет реагировать на сообщения, если перед ними не стоит обращение к боту
     */
    public static function UseAppeal($bool)
    {
        self::$useAppeal = $bool;
    }

    /**
     * @param bool $bool
     */
    public static function SetStrict($bool)
    {
        self::$strictCheck = $bool;
    }

    /**
     * @param array|string $appeal
     * @return void
     * Добавляет обращение
     */
    public static function AddAppeal($appeal)
    {
        if (is_array($appeal)) {
            array_map(function ($item) {
                self::AddAppeal($item);
            }, $appeal);
        } else {
            if (!self::$strictCheck) {
                $appeal = self::FormatAppeal($appeal);
            }
            self::$appealList[] = $appeal;
        }
    }

    /**
     * @return void
     * Запускает проверку
     */
    public static function Run()
    {
        $message = BotGet::Message();
        if (self::$ignoreOtherMessages) {
            $firstChar = mb_substr($message, 0, 1);
            if ($firstChar != self::$noIgnoreChar) {
                return;
            }
            $message = mb_substr($message, 1);
        }

        $message = explode(' ', $message);

        if (self::$useAppeal) {
            $appeal = array_shift($message);
            if (!self::$strictCheck) {
                $appeal = self::FormatAppeal($appeal);
            }

            if (!in_array($appeal, self::$appealList)) {
                return;
            }
        }

        $command = array_shift($message);

        if (!isset(self::$functionList[$command])) {
            $str = str_replace('{command}', $command, CONFIG['data']['unknownCommand']);
            BotMessage::Send($str);
            return;
        }

        $response = FunctionHandler::Call(self::$functionList[$command], $message);

        if (mb_substr($response, 0, 1) == CONFIG['data']['errorChar']) {
            $str = str_replace(['{command}', '{error}'], [$command, mb_substr($response, 1)], CONFIG['data']['error']);
            BotMessage::Send($str);
            return;
        }

        BotMessage::Send($response);
    }

    private static function FormatAppeal($appeal)
    {
        $lastChar = mb_substr($appeal, mb_strlen($appeal) - 1);

        if ($lastChar == ',') {
            $appeal = mb_substr($appeal, 0, -1);
        }

        return mb_strtolower($appeal);
    }
}