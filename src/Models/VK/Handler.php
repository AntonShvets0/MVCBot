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
     * @var array
     * Символ, с которого должно начинаться сообщение (только для Игнор Режима)
     */
    private static $noIgnoreChar = [];

    /**
     * @var array
     * Список функций
     */
    private static $functionList = [];

    /**
     * @param string|array $function
     * @param mixed $controller
     * @param bool $onlyPayLoad
     * @return bool
     * Регистрирует новую функцию
     */
    public static function Register($function, $controller, $onlyPayLoad = false)
    {
        if (is_array($function)) {
            array_map(function ($item) use ($controller, $onlyPayLoad) {
                self::Register($item, $controller, $onlyPayLoad);
            }, $function);
        } else {
            if (!self::$strictCheck) {
                $function = mb_strtolower($function);
            }
            if (isset(self::$functionList[$function])) {
                Logger::Warning("Function {$function} exists");
                return false;
            }
            self::$functionList[$function] = [$controller, $onlyPayLoad];
        }
        return true;
    }

    /**
     * @param bool $bool
     * @return void
     * Включает/Отключает "игнор мод", когда бот не реагирует на сообщения, если они не начинаются на определенную букву
     */
    public static function StartsWith($bool = true)
    {
        self::$ignoreOtherMessages = $bool;
    }

    /**
     * @param array|string $char
     * @return void
     * Добавляет символы, с которых должно начинаться сообщение
     */
    public static function AddStartsWith($char)
    {
        if (is_array($char)) {
            array_map(function ($item) {
                self::AddStartsWith($item);
            }, $char);
            return;
        }

        if (!self::$strictCheck) {
            $char = mb_strtolower($char);
        }

        self::$noIgnoreChar[] = $char;
    }

    /**
     * @param bool $bool
     * Включает/Отключает функцию обращений. Бот не будет реагировать на сообщения, если перед ними не стоит обращение к боту
     */
    public static function Appeal($bool)
    {
        self::$useAppeal = $bool;
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
            return;
        }
        if (!self::$strictCheck) {
            $appeal = self::FormatAppeal($appeal);
        }
        self::$appealList[] = $appeal;
    }

    /**
     * @param bool $bool
     */
    public static function Strict($bool)
    {
        self::$strictCheck = $bool;
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
            if (!in_array($firstChar, self::$noIgnoreChar)) {
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

        if (!isset(self::$functionList[$command]) || (self::$functionList[$command][1] && !BotGet::hasPayLoad())) {
            $str = str_replace('{command}', $command, CONFIG['data']['unknownCommand']);
            BotMessage::Send($str);
            return;
        }

        $response = FunctionHandler::Call(self::$functionList[$command][0], $message);

        $message = $response;
        $attach = [];
        $keyBoard = [];

        if (is_array($response)) {
            $message = $response[0];
            $attach = $response[1];
            $keyBoard = $response[2];
        }

        if (mb_substr($message, 0, 1) == CONFIG['data']['errorChar']) {
            $str = str_replace(['{command}', '{error}'], [$command, mb_substr($response, 1)], CONFIG['data']['error']);
            BotMessage::Send($str, 'callback', $attach, $keyBoard);
            return;
        }

        BotMessage::Send($response, 'callback', $attach, $keyBoard);
    }

    /**
     * @param $appeal
     * @return string
     * Форматирует обращение
     */
    private static function FormatAppeal($appeal)
    {
        $lastChar = mb_substr($appeal, mb_strlen($appeal) - 1);

        if ($lastChar == ',') {
            $appeal = mb_substr($appeal, 0, -1);
        }

        return mb_strtolower($appeal);
    }
}