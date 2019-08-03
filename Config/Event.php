<?php

/**
 * Данный файл нужен для регистрации EVENT-ов
 */

BotEvent::Register('message_new', function () {
    return 'Hello World';
});