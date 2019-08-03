<?php

/**
 * Данный файл нужен для регистрации команд бота
 */

Handler::SetStrict(true); // Выставляем строгую проверку обращений, и команд
Handler::UseAppeal(true); // Включаем режим обращений

Handler::AddAppeal('Bot'); // Добавляем обращение

Handler::Register('hello-world', function () {
    return 'Hello world';
});