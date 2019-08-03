<?php

/**
 * Данный файл нужен для регистрации EVENT-ов
 */

Event::Register('confirmation', function () {
    exit(CONFIG['vkCallbackData']['confirm']);
});

Event::Register('message_new', function () {
    Handler::Run();
});