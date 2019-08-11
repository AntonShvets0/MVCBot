<?php
/**
 * @author LordOverLord0
 * https://github.com/AntonShvets0/MVCBot
 * MVC CALLBACK BOT
 */

if (version_compare(phpversion(), '7.0.0', '<')) {
    exit('PHP версия должна быть больше 7.00');
}

if (!extension_loaded('curl') || !extension_loaded('mbstring')) {
    exit('У вас не установлено расширение CURL, или mbstring');
}

/*========БАЗОВЫЕ НАСТРОЙКИ========*/
/*
 *  Определяет степень логирования действий
 *  Степень хранит в себе степени ниже. Т.е третья степень будет логировать запросы к api, ошибки в api, и приходящие callback запросы
 *  0 — Нет логирования,
 *  1 — ошибки в API,
 *  2 — запросы к API,
 *  3 — приходящие CALLBACK запросы
 *  4 — Ошибки PHP
 *  5 — Предупреждения PHP
 */
const LOGGER_LEVEL = 2;

const CONFIG_FILE = 'json'; // Если желаете хранить данные в JSON -- измените значение на json, если в INI — измените значение на ini

mb_internal_encoding('utf-8'); // Устанавливаем кодировку

define('ROOT', $_SERVER['DOCUMENT_ROOT']); // Главная папка проекта

if (CONFIG_FILE == 'ini') {
    define('CONFIG', parse_ini_file(ROOT . '/Config/config.ini', true)); // Получаем настройки
} elseif (CONFIG_FILE == 'json') {
    define('CONFIG', json_decode(file_get_contents(ROOT . '/Config/config.json'), true));
} else {
    exit('Unknown CONFIG_FILE: ' . CONFIG_FILE);
}

/*========БАЗОВЫЕ НАСТРОЙКИ========*/

array_map(function ($item) {
    require_once $item;
}, GetFile('Models')); // Инклудим все файлы в папке Models

/* ВКЛЮЧАЕМ ПОКАЗ ОШИБОК */
if (LOGGER_LEVEL >= 4) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    if (LOGGER_LEVEL >= 5) {
        set_error_handler(function () {
            $args = array_slice(func_get_args(), 1);
            Logger::Warning("Warning: {$args[0]}, file {$args[1]}, line {$args[2]}", 5, true);
        });
    }
    if (LOGGER_LEVEL >= 4) {
        set_exception_handler(function (Throwable $exception) {
            $message = $exception->getMessage();
            $line = $exception->getLine();
            $file = $exception->getFile();
            Logger::Error("Error: {$message}, file {$file}, line {$line}", 4, true);
        });
    }
}
/* ВКЛЮЧАЕМ ПОКАЗ ОШИБОК */

/* ИНКЛУДИМ ГЛАВНЫЕ ФАЙЛЫ */

require_once ROOT . '/Config/Event.php';
require_once ROOT . '/Config/Command.php';

require_once ROOT . '/Config/Manager.php';

/* ИНКЛУДИМ ГЛАВНЫЕ ФАЙЛЫ */

function GetFile($path) // Получаем все файлы в папке
{
    $files = scandir($path, true);
    $pathList = [];

    foreach ($files as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        } elseif (is_dir(ROOT . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file)) {
            $pathList = array_merge($pathList, GetFile($path . DIRECTORY_SEPARATOR . $file));
        } else {
            $pathList[] = ROOT . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file;
        }
    }

    return $pathList;
}
