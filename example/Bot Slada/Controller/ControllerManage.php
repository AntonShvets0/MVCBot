<?php


class ControllerManage
{

    function ActionAdmin($level = 0)
    {
        if (isset(VK['fwd_messages'][0]['from_id'])) {
            $id = VK['fwd_messages'][0]['from_id'];
        } else {
            $id = Utils::Join(array_slice(func_get_args(), 1), ' ');

            $id = Account::GetID($id);

            if ($id == 0) {
                return '@Ой-ой! Я таких не знаю ((';
            }
        }

        $rankUser = Account::GetRank($id);
        $rank = Account::GetRank();
        if ($rankUser >= $rank) {
            return '@Нельзя изменить уровень человеку, который выше или равный вам по полномочиям';
        }

        if ($level >= $rank) {
            return '@Нельзя изменить уровень на выше, или равный вам';
        }

        if ($id == BotGet::From()) {
            return '@Нельзя изменить уровень себе';
        }

        Account::SetRow($id, ['rank' => $level]);

        $name = BotGet::Name(true, 'nom', $id);
        $rankName = Account::GetRankName($level);
        return "Пользователю @id{$id} ({$name}) установлен ранг {$rankName} ({$level})";
    }

    function ActionAccess($command = "", $level = 0)
    {
        if (empty($command)) {
            return '@Правильный ввод: Sweety, права <Команда> [Ранг]';
        }

        $data = Account::GetData();
        if ($level > $data['countRanks'] || $level < 0) {
            return '@Указанный ранг не существует';
        }
        $command = mb_strtolower($command);

        if (!isset(Handler::$functionList[$command])) {
            return '@Неизвестная команда';
        }

        $data = Sql::Select()->From('commands')->Where('command', $command)->Exec();

        if (!isset($data[0]['id'])) {
            Sql::Insert(['conversation' => BotGet::Peer(), 'command' => $command, 'rank' => $level])->From('commands')->Exec();
        } else {
            Sql::Update(['rank' => $level])->From('commands')->Where('conversation', BotGet::Peer())->Where('command', $command)->Exec();
        }

        $name = Account::GetRankName($level);
        return "Команда \"{$command}\" теперь доступна только с ранга {$name} ({$level})";
    }

    function ActionRankName($level = 0)
    {
        if (mb_strlen($level) < 1) {
            return '@Ранг не указан';
        }

        $name = Utils::Join(array_slice(func_get_args(), 1), ' ');

        if (empty($name)) {
            return '@Имя ранга не указано';
        }

        $data = Account::GetData();
        if ($data['countRanks'] < $level || $level < 0) {
            return '@Ранг не существует';
        }

        $rank = json_decode($data['ranks'], true);

        $rank[$level] = $name;

        $rank = json_encode($rank, JSON_UNESCAPED_UNICODE);

        Sql::Update(['ranks' => $rank])->From('conversations')->Where('id', BotGet::Peer())->Exec();

        return "Имя {$level} ранга установлено на {$name}";
    }

    function ActionRankAdd($count = 0)
    {
        if (empty($count)) {
            return '@Нельзя добавить 0 рангов';
        }

        if ($count > 15) {
            return '@Нельзя указать больше 15 рангов за раз';
        }

        $default_names = Utils::Join(array_slice(func_get_args(), 1), ' ');

        $nameRanks = [];

        for ($i = 0; $i < $count; $i++) {
            $nameRanks[$i] = "Безымянный";
        }

        if (!empty($default_names)) {
            $oldNameRanks = $nameRanks;
            $nameRanks = explode(',', $default_names);
            if (count($nameRanks) > $count ||count($nameRanks) < $count) {
                BotMessage::Send('Вы указали неверное количество имен. Параметр [Имена через запятую] будет опущен');
                $nameRanks = $oldNameRanks;
            }
        }

        $data = Account::GetData();

        $ranks = json_encode(array_merge(json_decode($data['ranks'], true), $nameRanks), JSON_UNESCAPED_UNICODE);
        $countRanks = $data['countRanks'] + $count;

        Sql::Update(['ranks' => $ranks, 'countRanks' => $countRanks])->From('conversations')->Where('id', BotGet::Peer())->Exec();
        Sql::Update(['rank' => $countRanks])->From('users')->Where('user', BotConversation::GetOwnerID())->Where('conversation', BotGet::Peer())->Exec();

        return Utils::Num2Str($count, 'Добавлен', ['', 'о', 'о']) . ' '  . $count . ' ' . Utils::Num2Str($count, 'ранг', ['', 'а', 'ов']);
    }

    function ActionRankRemove($count = 0)
    {
        if (empty($count)) {
            return '@Количество рангов не указано';
        }

        $data = Account::GetData();

        if ($count > $data['countRanks']) {
            return '@Столько рангов не существует';
        }

        $ranks = json_decode($data['ranks'], true);

        for ($i = 0; $i < $count; $i++) {
            unset($ranks[$data['countRanks'] - $i]);
        }

        $ranks = json_encode($ranks, JSON_UNESCAPED_UNICODE);

        Sql::Update(['countRanks' => $data['countRanks'] - $count, 'ranks' => $ranks])->From('conversations')->Where('id', BotGet::Peer())->Exec();
        Sql::Update(['rank' => $data['countRanks'] - $count])->From('commands')->Where('conversation', BotGet::Peer())->Where('rank', '>', $data['countRanks'] - $count)->Exec();

        $rank = $data['countRanks'] - $count - 1 < 0 ? 0 : $data['countRanks'] - $count - 1;
        Sql::Update(['rank' => $rank])->From('users')->Where('conversation', BotGet::Peer())->Exec();
        Sql::Update(['rank' => $data['countRanks'] - $count])->From('users')->Where('conversation', BotGet::Peer())->Where('user', BotConversation::GetOwnerID())->Exec();
        return Utils::Num2Str($count, 'Удален', ['', 'о', 'о']) . ' ' . $count . ' ' . Utils::Num2Str($count, 'ранг', ['', 'а', 'ов']);
    }

    function ActionListRank()
    {
        $data = json_decode(Account::GetData()['ranks'], true);

        $msg = "СПИСОК РАНГОВ:<br>";
        foreach ($data as $key => $val) {
            $msg .= "{$key}. {$val}<br>";
        }

        return $msg;
    }

    function ActionListCommand()
    {
        $data = Sql::Select()->From('commands')->Where('conversation', BotGet::Peer())->Exec();

        $msg = "СПИСОК КОМАНД:<br>";
        foreach ($data as $val) {
            $rank = Account::GetRankName($val['rank']);
            $msg .= "{$val['command']} -- {$rank} ({$val['rank']})<br>";
        }

        return $msg;
    }

    function ActionGame()
    {
        $data = !Account::GetData()['game'];
        Sql::Update(['game' => $data])->From('conversations')->Exec();
        return 'Развлекательные команды ' . ($data ? 'включены' : 'выключены');
    }
}