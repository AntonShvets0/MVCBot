<?php

/**
 * Данный файл нужен для регистрации команд бота
 */

Handler::IgnoreMode(true, '/'); // Включает игнор мод. Бот будет игнорить любые сообщения, без "/" в начале

Handler::Register('hello-world', function () {
    return 'Hello world';
});
