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


$keyBoard = [
    true, // Скрывать ли клавиатуру после нажатия. false/true

    [ // первая строка
        "Первая кнопка" => [ // Название кнопки
            "clr" => "primary", // цвет,
            "cmd" => ["hello", "world"] // payload
        ]
    ],
    [ // Вторая строка
        "Вторая кнопка" => [ // Название кнопки
            "clr" => "negative", // цвет,
            "cmd" => ["hello", "world2"] // payload
        ]
    ]
];
