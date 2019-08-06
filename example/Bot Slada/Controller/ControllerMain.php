<?php


class ControllerMain
{
    function ActionAbout()
    {
        return 'Этот бот предназначен для управления группой. Ничего более<br>Бот создан на библиотеке MVC PHP Bot: https://github.com/AntonShvets0/MVCBot/';
    }

    function ActionHelp($page = 1)
    {
        if (!($page == '1' || $page == '2')) {
            return '@Неверная страница';
        }

        if ($page == '1') {
            $message = Account::Gender() . ', ты видишь перед собой все возможные команды бота!<br>P.s: Не забывай дописывать перед командой "Sweety ", договорились? :-) <br>P.s: Все параметры указанные в <вот таких скобках> -- обязательные, а [вот в таких] необязательные!<br>Список команд:<br><br>';

            $message .= file_get_contents(ROOT . '/Data/Help.txt');
        } else {
            $message = Account::OwoGender() . '! Пришло время веселиться, не так ли?<br>Перед тобой все команды, которыми я могу тебя развлечь. Удачи :-)<br>P.s: Не забывай дописывать "Sweety ", перед командой<br>';
            $message .= file_get_contents(ROOT . '/Data/Help2.txt');
        }

        return $message;
    }

    function ActionInstall()
    {
        $owner = BotConversation::GetOwnerID();

        $registered = false;
        if (Account::Registered()) {
            $registered = true;
            Sql::Delete()->From('conversations')->Where('id', BotGet::Peer())->Exec();
            Sql::Delete()->From('users')->Where('conversation', BotGet::Peer())->Exec();
            Sql::Delete()->From('commands')->Where('conversation', BotGet::Peer())->Exec();
        }
        if (!BotConversation::IsAdmin()) {
            return '@' . Account::OwoGender() . '! *Гневно топнула ножкой*. Я не администратор!';
        }

        $ranks = ['Пользователь', 'Помощник', 'Администратор', 'Главный Администратор'];
        $commands = ['антивыход' => 3, 'кик' => 2, 'бан' => 2, 'разбан' => 1, 'мут' => 1, 'размут' => 1, 'предупреждение' => 2, 'снятьпред' => 1, 'админ' => 3, 'установить' => 3, 'права' => 3, 'рангимя' => 3, 'рангдобавить' => 3, 'рангудалить' => 3, 'название' => 1, 'развлкоманды' => 1];

        foreach ($commands as $command => $rank) {
            Sql::Insert(['rank' => $rank, 'conversation' => BotGet::Peer(), 'command' => $command])->From('commands')->Exec();
        }

        $data = ['id' => BotGet::Peer(), 'name' => BotConversation::GetName(), 'countRanks' => 3, 'ranks' => json_encode($ranks, JSON_UNESCAPED_UNICODE), 'game' => 1];
        $user = ['user' => $owner, 'conversation' => BotGet::Peer(), 'rank' => 3];
        Sql::Insert($data)->From('conversations')->Exec();
        Sql::Insert($user)->From('users')->Exec();

        return 'Ту-ту-ру~. Команды бота ' . (!$registered ? 'установлены' : 'переустановлены') . '! <3';
    }
}