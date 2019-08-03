<img src="logo.png">
<h1>MVC BOT</h1>
<hr>
Данная библиотека облегчает создание ботов для VK через CallBack API
в несколько сотен раз<br>
Добавив ее в проект, можно создать Hello World бота за несколько секунд.<br>
Что же она может?
<br>
<h2>Создание команд</h2>
<code>Handler::Register('hello-world', function () {<br>
    return 'Hello world';
<br>});
</code><br>
Как вы можете видеть в коде выше -- это делается в пару строк.<br>
Вы вызываете метод Register из класса Handler в файле /Config/Command.php<br>
<br>
Первый аргумент -- это название функции. Может быть или string, или array. <br> 
Второй -- это сама функция, которая вызовется в случае набора команды. Это может быть или анонимная функция, или строка
<br>
Если это строка, то там указывается контроллер, и функция через @.<br>
Т.е, строка:<br>
Hello@World<br>
Создаст экземпляр класса ControllerHello из файла /Controller/ControllerHello.php, и вызовет метод ActionWorld<br>
<br>
Так-же, можно передавать аргументы:<br>
<code>Handler::Register('name', function ($name, $age) {<br>
    return "Hello, {$name} ({$age} years old)";
<br>});
</code><br>
При отправке боту такого сообщения:<br>
name Антон 17<br>
Человеку в ответ бот отошлет "Hello, Антон (17 years old)"
<br>
<h2>Регистрация Event'ов</h2>
Допустим, нам надо захватить приходящий от VK тип message_new, как это сделать?<br>
Пишем такие строчки в файле /Config/Event.php:<br>
<code>Event::Register('message_new', function () {<br>
          Handler::Run();<br>
          Logger::Info("Пришел тип message_new");<br>
      });
</code><br>
Теперь анонимная функция (или строка) которую вы указали во втором аргументе, будет вызываться каждый раз, когда будет приходить этот тип