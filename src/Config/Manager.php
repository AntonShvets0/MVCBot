<?php

// Этот файл обрабатывает CALLBACK от VK

$callback = file_get_contents('php://input');
$data = json_decode($callback, true);

define('VK', $data['object'] ?? '');

if (isset(CONFIG['vkCallbackData']['secret'])) {
    $data['secret'] = $data['secret'] ?? '';
    if ($data['secret'] != CONFIG['vkCallbackData']['secret']) {
        exit();
    }
}

if (Handler::GetTimeout() != '*') {
    if (time() - $data['object']['date'] >= Handler::GetTimeout()) {
        exit('ok');
    }
}

define('GROUP', $data['group_id']);
Logger::Info("Request CALLBACK data: {$callback}", 3);

if (isset(Event::$event[$data['type']])) {
    FunctionHandler::Call(Event::$event[$data['type']]);
}

echo 'ok';