<?php

/**
 * Данный файл нужен для регистрации команд бота
 */

/* Включает игнор мод. Бот будет игнорить любые сообщения, без "/" или "$" в начале */
Handler::IgnoreMode(true);
Handler::AddNoIgnoreChar(['/', '$']);
/* Включает игнор мод. Бот будет игнорить любые сообщения, без "/" или "$" в начале */


Handler::Register('hello-world', function () { // Регистрируем команду hello-world. На эту фразу, бот будет отсылать пользователю Hello World
    return 'Hello world';
});

Handler::Register('button-test', function () { // Отправляет клавиатуру пользователю
    $keyboard = [
        true, // после нажатия на какую-то кнопку, клавиатура скроется

        [
            "О нас" => [
                "positive", // зеленая кнопка
                ['about', 'us'] // при нажатии на эту кнопку, боту отправит команду "about us"
            ]
        ]
    ];
    return ['Проверка клавиатуры', ['photo433245412_457246880', 'photo306334670_457253681'], $keyboard];
});

Handler::Register('about', function () {
    return 'MVC BOT -- это удобная библиотека для создания CALLBACK-ботов в вк. В ней встроена библиотека для работы с VK API, а создание команд делается в пару строк кода<br>Автор: https://vk.com/lordoverlord0';
}, true); // делаем команду доступной только через кнопку

Handler::Register(['рандом', 'ранд', 'random', 'rand'], 'main@random'); // Регистрируем команды (рандом/ранд/random/rand) которые ссылаются на класс ControllerMain и метод ActionRandom

Handler::Register(['управл-беседа'], function ($type) { // управляем беседой
    $data = Utils::Join(array_slice(func_get_args(), 1), ' '); // Получаем всю информацию после $type

    switch ($type) {
        case 'удалить':
            BotConversation::DeleteUser($data); // Удаляем пользователя из беседы
            return 'Пользователь @id' . $data . ' удален';
        case 'изменить_имя':
            BotConversation::Title($data); // Изменяем имя беседы
            return 'Успешно';
        case 'удалить_фото':
            BotConversation::DeletePhoto(); // Удаляем фото беседы
            return 'Успешно';
        default:
            return '@Неизвестный тип: ' . $type;
    }
});

Handler::Register('эмуляция', function ($type) { // эмулируем набор текста, или запись голосового сообщения
    $type = $type == 'голосовуха' ? false : true;
    BotMessage::Activity($type);
});