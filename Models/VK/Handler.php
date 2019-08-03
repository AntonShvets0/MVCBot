<?php

/**
 * @class Handler
 * Этот класс нужен для того, чтобы регистрировать новые функции в боте, удалять их
 */

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

    }

    /**
     * @param bool $bool
     * Включает/Отключает функцию обращений. Бот не будет реагировать на сообщения, если перед ними не стоит обращение к боту
     */
    public static function UseAppeal($bool)
    {

    }

    /**
     * @param bool $bool
     */
    public static function SetStrict($bool)
    {

    }

    /**
     * @param array|string $appeal
     * Добавляет обращение
     */
    public static function AddAppeal($appeal)
    {

    }

    /**
     * @return void
     * Запускает проверку
     */
    public static function Run()
    {

    }
}