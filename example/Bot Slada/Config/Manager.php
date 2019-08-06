<?php

// Этот файл обрабатывает CALLBACK от VK

$data = json_decode(file_get_contents('php://input'), true);

define('VK', isset($data['object']) ?  $data['object'] : '');

if ($data['secret'] != CONFIG['vkCallbackData']['secret']) {
    exit();
}

define('GROUP', $data['group_id']);

if (isset(Event::$event[$data['type']])) {
    FunctionHandler::Call(Event::$event[$data['type']]);
}

echo 'ok';