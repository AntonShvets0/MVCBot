<?php

/**
 * @class FunctionHandler
 * Вызывает функцию
 */

class FunctionHandler
{

    /**
     * @param mixed $function
     * @param array $data
     * @return string
     */
    public static function Call($function, $data = [])
    {
        if (is_string($function) && strrpos($function, '@')) {
            @list($controller, $action) = explode('@', $function);

            $controller = 'Controller' . ucfirst($controller);
            $action = 'Action' . ucfirst($action);

            if (!file_exists(ROOT . '/Controller/' . $controller . '.php')) {
                Logger::Add("File /Controller/{$controller}.php not exists", 'Error');
                return "";
            }

            require_once ROOT . '/Controller/' . $controller . '.php';

            $class = new $controller;

            if (!method_exists($class, $action)) {
                Logger::Add("Method {$action} not exists in class {$controller} (/Controller/{$controller}.php)", "Error");
                return "";
            }

            $response = call_user_func_array([$class, $action], $data);
        } else {
            $response = call_user_func_array($function, $data);
        }

        return $response;
    }
}