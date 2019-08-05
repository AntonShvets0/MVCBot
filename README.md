<p align="center">
<img align="center" src="/img/logo.png">
<h1 align="center">MVC BOT</h1>
<h2 align="center">Содержание</h2>
<p align="center">
<a href="#создание-команд" align="center">1. Создание команд</a><br>
<a href="#регистрация-eventов" align="center">2. Регистрация Event-ов</a><br>
<a href="#дополнительно" align="center">3. Дополнительно</a><br>
<a href="#встроенный-логгер" align="center">4. Встроенный Логгер</a><br>
<a href="#клавиатура-ботов" align="center">5. Клавиатура Ботов</a><br>
<a href="#встроенные-классы-для-работы-с-vk-api" align="center">6. Встроенные классы для работы с VK API</a><br>
</p>
</p>
<hr>
Данная библиотека облегчает создание ботов для VK через CallBack API
в несколько сотен раз<br>
Добавив ее в проект, можно создать Hello World бота за несколько секунд.<br>
Что же она может?
<br>
<h2>Создание команд</h2>

```php
Handler::Register('hello-world', function () {
    return 'Hello world';
}, false);
```
Как вы можете видеть в коде выше — это делается в пару строк.<br>
Вы вызываете метод Register из класса Handler в файле `/Config/Command.php` <br>
<br>
Первый аргумент — это название функции. Может быть или string, или array. <br> 
Второй — это сама функция, которая вызовется в случае набора команды. Это может быть или анонимная функция, или строка<br>
Если это строка, то там указывается имя класса, и метод через @.<br>
Т.е, строка: `Hello@World` <br>
Вызовет из класса ControllerHello (который находится в файле `/Controller/ControllerHello.php` ) метод `ActionWorld` <br>
Третий аргумент — необязательный для указания параметр. Если он установлен на true, то доступ к этой команде будет возможен только через клавиатуру ботов (payload).
<br>
Так-же, можно передавать аргументы:<br>
```php
Handler::Register('name', function ($name, $age) {
    return "Hello, {$name} ({$age} years old)";
});
```
<br>
<img src="/img/nameanton17.jpg">
<br>
<h2>Регистрация Event'ов</h2>
Допустим, нам надо захватить приходящий от VK тип message_new, как это сделать?<br>
Пишем такие строчки в файле 

`/Config/Event.php`

```php
Event::Register('message_new', function () {
      Handler::Run();
      Logger::Info("Пришел тип message_new");
});
```

Теперь анонимная функция (или строка) которую вы указали во втором аргументе, будет вызываться каждый раз, когда будет приходить этот тип
<h2>Дополнительно</h2>
<hr>
Конечно-же, это не все возможности библиотеки.<br>
<h3>Обращения к боту</h3><br>Бота можно заставить игнорировать сообщения, в которых не присутствует обращение к нему. Это делается так:<br>

```php
Handler::Appeal(true);
Handler::AddAppeal('Bot');
```
<small>Этот код будет работать только в файле ` /Config/Command.php ` , или ` /Config/Event.php` </small><br>
Теперь, бот не будет отзываться на такие сообщения:<br>
name Антон 17<br>
Но, будет отзываться на:<br>
Bot name Антон 17<br>
Так-же, в первый аргумент метода AddApeal можно передать не только string, но и array.<br>
```php
Handler::AddApeal(['Бот', 'Ботец']);
```
Теперь, бот будет отзываться и на Бот, и на Ботец.

<h3>Игнор-режим</h3>
Еще, бота можно заставить игнорировать сообщения, которые не начинаются с определенного символа.<br>

```php
Handler::StartsWith($bool);
Handler::AddStartsWith($char);
```

$char — символ, с которого должно начинаться сообщение. Это может быть array, или string.<Br> Т.е, такой код:

```php
Handler::StartsWith(true);
Handler::AddStartsWith(['/', '$']);
```

Сделает так, что вот такие обращения не будут работать:<br>
name Антон 17<br>
А такие будут:<br>
/name Антон 17<br>
$name Антон 17

<h3>Строгая проверка</h3><br>
Можно включить строгую проверку. Она будет сверять регистр функций, и обращений.<br>

Допустим, у нас есть обращение "Бот", и пользователь пишет:<br>
бот, name Антон 17<br>
При выключенной строгой проверке, это сообщение вызовет функцию name<br>
При включенной — выведет ошибку о том, что такая функция не обнаружена.

Включить строгую проверку: 
```php
Handler::Strict(true);
```
<small>Включение строгой проверки нужно поместить в начало файла Config/Command.php, иначе не будет работать.</small>
<h3>Ошибки</h3><br>
Согласитесь, неудобно писать каждый раз, при ошибке:<br>

```php
return "ОШИБКА: Неизвестный символ";
```

<br>
Именно поэтому, я сделал такую фишку. Если в возвращаемом значении, первый символ это @, то это выведет как ошибку:<br>

```php
return "@Неизвестный символ";
```


<br>Код выше, отправит сообщение пользователю такое: "ОШИБКА: Неизвестный символ"<br>
<small>Символ можно сменить с @, на любой другой, в файле `/Config/config.ini` </small>
<h2>Встроенный логгер</h2>
<hr>
В библиотеку встроен логгер. Методы логгера ниже:<br>

```php
// Создает запись в логах, с типом Default
Logger::Info($message): void

// Создает запись в логах, с типом Error
Logger::Error($message): void

// Создает запись в логах, с типом Warning
Logger::Warning($message): void
```
<h2>Клавиатура ботов</h2>
<hr>
Я немного изменил синтаксис клавиатуры ботов. Т.к оригинальный синтаксис от ВК меня пугает.<br>
Чтобы создать клавиатуру, в моей библиотеке следует создать простой массив:

```php

$keyBoard = [
    true, // Скрывать ли клавиатуру после нажатия. false/true

    [ // первая строка
        "Первая кнопка" => [ // Название кнопки
            "primary", // цвет,
            ["hello", "world"] // payload
        ]
    ],
    [ // Вторая строка
        "Вторая кнопка" => [ // Название кнопки
            "negative", // цвет,
            ["hello", "world2"] // payload
        ]
    ]
];
```


<h2>Встроенные классы для работы с VK API</h2>
<hr>
В библиотеке встроены классы для работы с VK API. Список методов ниже:<br>

```php
class BotMessage {
    // Отсылает сообщение пользователю. Если $id равняется callback, то это отправит тому пользователю, от которого пришел callback сайту.
    public static function Send($message, $id = 'callback', $attach = [], $keyBoard = []): bool

    // Удаляет сообщение
    public static function Delete($messageID): bool
    
    // Если $text == false, то бот будет записывать голосовое сообщение, если true, то будет печатать сообщение
    public static function Activity($text = true, $peerID = 'callback'): bool

    // Редактирует сообщение
    public static function Edit($messageID, $text, $attach = [], $peerID = 'callback'): bool
}

class BotConversation {

    // Возвращает имя беседы
    public static function GetName(): string
    
    // Возвращает ID создателя
    public static function GetOwnerID(): int
    
    // Возвращает ID админов
    public static function GetAdminsID(): array
    
    // Возвращает информацию из метода messages.getConversationsById
    public static function GetInfo($peerID = 'callback', $fields = []): array
    
    // Удаляет пользователя из беседы
    public static function DeleteUser($user, $peerID = 'callback'): bool
    
    // Закрепляет сообщение
    public static function Pin($messageID, $peerID = 'callback'): bool
    
    // Изменяет заголовок беседы
    public static function Title($title, $peerID = 'callback'): bool
}

class BotRequest {
    // Вызывает метод $method из VK API и возвращает массив с результатом.
    public static function API($method, $data = []): array|false
}

class BotGet {
    // Возвращает from_id
    public static function From(): int
    
    // Возвращает peer_id
    public static function Peer(): int
    
    // Возвращает сообщение пользователя
    public static function Message(): string
    
    // Возвращает true, если пользователь отправил PayLoad вместе с сообщением
    public static function HasPayLoad(): bool
    
    // Возвратит 1, если пользователь с таким id мужчина, и 0, если женщина.
    public static function Gender($id = 'callback'): int
    
    // Возвращает имя и фамилию пользователя. Если $lastName = false, то фамилию не будет возвращать
    public static function Name($lastName = true, $nameCase = 'nom', $id = 'callback'): string
    
    // Возвращает информацию о пользователях. $ids может быть массивом, и строкой. 
    public static function Info($ids = 'callback', $fields = [], $nameCase = 'nom'): array
}

class BotWall {
    // Добавляет комментарий к посту. Если $ownerID указан как callback, то возьметься id группы бота
    public static function AddComment($postID, $text, $ownerID = 'callback', $attach = [], $replyTo = 0, $stickerId = 0): void
    
    // Открывает/Закрывает комментарии к посту. $type может быть только open, или close. 
    public static function Comment($postID, $type = 'close'): void
}

``` 