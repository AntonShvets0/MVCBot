<?php
/**
 * @version 0.1
 * @author LordOverLord0
 * https://github.com/AntonShvets0/MVCBot
 * MVC CALLBACK BOT
 */

/*========БАЗОВЫЕ НАСТРОЙКИ========*/
const DEBUG = true; // Если нужно отключить дебаг режим -- поставьте false
const CONFIG_FILE = 'json'; // Если желаете хранить данные в JSON -- измените значение на json, если в INI -- измените значение на ini

mb_internal_encoding('utf-8'); // Устанавливаем кодировку

/* ВКЛЮЧАЕМ ПОКАЗ ОШИБОК */
if (DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
/* ВКЛЮЧАЕМ ПОКАЗ ОШИБОК */

// define('ROOT', dirname(__FILE__)); // Главная папка проекта

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
