<?php


class ControllerAdmin
{
    function ActionAntiLeave()
    {
        $bool = !Account::GetData()['antiLeave'];

        Sql::Update(['antiLeave' => $bool])->From('conversations')->Exec();
        return $bool ? 'Анти-Выход включен' : 'Анти-Выход выключен';
    }

    function ActionKick()
    {
        $id = Utils::Join(func_get_args(), ' ');

        if (empty($id) && isset(VK['fwd_messages'][0])) {
            $id = VK['fwd_messages'][0]['from_id'];
        } elseif (empty($id)) {
            return '@Вы не указали id';
        } else {
            $id = Account::GetID($id);
            if ($id == 0) {
                return '@Ой-ой! Я таких не знаю ((';
            }
        }

        $rank = Account::GetRank();
        $rankKick = Account::GetRank($id);

        if ($rankKick >= $rank) {
            $name = BotGet::Name(true, 'gen', $id);

            return "Не удалось кикнуть @id{$id} ($name)";
        }

        $response = BotConversation::DeleteUser($id);
        if ($response) {
            $name = BotGet::Name(true, 'nom', $id);

            return "Пользователь @id{$id} ($name) кикнут";
        } else {
            $name = BotGet::Name(true, 'gen', $id);

            return "Не удалось кикнуть @id{$id} ($name)";
        }
    }

    function ActionBan($id = 0)
    {
        if (isset(VK['fwd_messages'][0]['from_id'])) {
            $id = VK['fwd_messages'][0]['from_id'];
            $desc = Utils::Join(func_get_args(), ' ');
        } elseif (!empty($id)) {
            $desc = Utils::Join(array_slice(func_get_args(), 1), ' ');
        } else {
            return '@Нужно указать ID человека, или его имя!';
        }

        $id = Account::GetID($id);

        if ($id == 0) {
            return '@Ой-ой! Я таких не знаю ((';
        }

        $rank = Account::GetRank();
        $rankKick = Account::GetRank($id);

        if ($rankKick >= $rank) {
            $name = BotGet::Name(true, 'gen', $id);

            return "Не удалось забанить @id{$id} ($name)";
        }

        $response = BotConversation::DeleteUser($id);

        if ($response) {
            Account::SetRow($id, ['ban' => 1, 'ban_desc' => $desc, 'ban_by' => BotGet::From()]);

            $name = BotGet::Name(true, 'nom', $id);

            return "Пользователь @id{$id} ($name) забанен";
        } else {
            $name = BotGet::Name(true, 'gen', $id);

            return "Не удалось забанить @id{$id} ($name)";
        }
    }

    function ActionUnBan()
    {
        if (isset(VK['fwd_messages'][0]['from_id'])) {
            $id = VK['fwd_messages'][0]['from_id'];
        } else {
            $id = Utils::Join(func_get_args(), ' ');
            if (empty($id)) {
                return '@Вы не указали ID';
            }
        }

        $id = Account::GetID($id);

        $data = Account::GetDataUser($id);

        if ($id == 0 || !$data) {
            return '@Ой-ой! Я таких не знаю ((';
        }

        Account::SetRow($id, ['ban' => 0, 'ban_desc' => '', 'ban_by' => '']);

        $name = BotGet::Name(true, 'nom', $id);

        return "Пользователь @id{$id} ($name) разбанен";
    }

    function ActionMute()
    {
        if (isset(VK['fwd_messages'][0]['from_id'])) {
            $id = VK['fwd_messages'][0]['from_id'];
        } else {
            $id = Utils::Join(func_get_args(), ' ');
            if (empty($id)) {
                return '@Следует указать ID, или имя пользователя';
            }
        }

        $id = Account::GetID($id);

        if ($id == 0) {
            return '@Ой-ой! Я таких не знаю ((';
        }

        $rank = Account::GetRank();
        $rankKick = Account::GetRank($id);

        if ($rankKick >= $rank) {
            $name = BotGet::Name(true, 'gen', $id);

            return "Не удалось дать затычку @id{$id} ($name)";
        }

        Account::SetRow($id, ['mute' => 1]);

        $name = BotGet::Name(true, 'nom', $id);

        return "Пользователь @id{$id} ($name) получил затычку";
    }

    function ActionUnMute()
    {
        if (isset(VK['fwd_messages'][0]['from_id'])) {
            $id = VK['fwd_messages'][0]['from_id'];
        } else {
            $id = Utils::Join(func_get_args(), ' ');

            $id = Account::GetID($id);

            if ($id == 0) {
                return '@Ой-ой! Я таких не знаю ((';
            }
        }

        Account::SetRow($id, ['mute' => 0]);

        $name = BotGet::Name(true, 'nom', $id);

        return "Пользователь @id{$id} ($name) был освобожден от затычки";
    }

    function ActionUnWarn()
    {
        if (isset(VK['fwd_messages'][0]['from_id'])) {
            $id = VK['fwd_messages'][0]['from_id'];
        } else {
            $id = Utils::Join(func_get_args(), ' ');

            $id = Account::GetID($id);

            if ($id == 0) {
                return '@Ой-ой! Я таких не знаю ((';
            }
        }

        $data = Account::GetDataUser($id);
        if ($data && $data['warn'] >= 1) {

            Account::SetRow($id, ['warn' => --$data['warn']]);

            $name = BotGet::Name(true, 'dat', $id);

            return "Пользователю @id{$id} ($name) снято одно предупреждение ({$data['warn']} / 3)";
        } else {
            return "У пользователя нет предупреждений";
        }
    }

    function ActionWarn()
    {
        if (isset(VK['fwd_messages'][0]['from_id'])) {
            $id = VK['fwd_messages'][0]['from_id'];
        } else {
            $id = Utils::Join(func_get_args(), ' ');

            $id = Account::GetID($id);

            if ($id == 0) {
                return '@Ой-ой! Я таких не знаю ((';
            }
        }

        $rank = Account::GetRank();
        $rankKick = Account::GetRank($id);

        if ($rankKick >= $rank) {
            $name = BotGet::Name(true, 'dat', $id);

            return "Не удалось дать предупреждение @id{$id} ($name)";
        }

        $data = Account::GetDataUser($id);
        $data['warn'] = !$data ? 0 : $data['warn'];

        Account::SetRow($id, ['warn' => ++$data['warn']]);
        Account::CheckWarn($id);

        $name = BotGet::Name(true, 'nom', $id);

        return "Пользователь @id{$id} ($name) получил предупреждение. ({$data['warn']} / 3)";
    }

    function ActionTitle()
    {
        $title = Utils::Join(func_get_args(), ' ');
        if (empty($title)) {
            return '@Вы не указали новое название беседы';
        }
        BotConversation::Title($title);
        return "Вы изменили имя беседы на \"{$title}\"";
    }
}