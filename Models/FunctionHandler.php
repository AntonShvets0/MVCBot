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
                Logger::Error("File /Controller/{$controller}.php not exists");
                return "";
            }

            require_once ROOT . '/Controller/' . $controller . '.php';

            if (!class_exists($controller)) {
                Logger::Error("Class {$controller} not exists in (/Controller/{$controller}.php)");
                return "";
            }

            $class = new $controller;

            if (!method_exists($class, $action)) {
                Logger::Error("Method {$action} not exists in class {$controller} (/Controller/{$controller}.php)");
                return "";
            }

            $response = call_user_func_array([$class, $action], $data);
        } else {
            try {
                $response = call_user_func_array($function, $data);
            } catch (Exception $exception) {
                Logger::Warning("Exception {$function}: {$exception->getMessage()}, {$exception->getTrace()}");
                return "";
            }
        }

        return $response;
    }
}