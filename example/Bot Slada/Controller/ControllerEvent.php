<?php


class ControllerEvent
{
    function ActionMessage()
    {
        if (isset(VK['action'])) {
            $this->ActionObject();
            return;
        }
        $data = Account::GetDataUser();
        if ($data && $data['mute']) {
            BotMessage::Send('@id' . BotGet::From() . ', на вас наложили мут. Не пишите ничего в чат, иначе, вам будет выдано еще одно предупреждение.<br>Сейчас у вас: ' . ++$data['warn'] . ' / 3 предупреждений');
            Account::SetRow(BotGet::From(), ['warn' => $data['warn']]);
            Account::CheckWarn();
            return;
        }

        Handler::Run();
    }

    function ActionObject()
    {
        switch (VK['action']['type']) {
            case 'chat_invite_user':
                $id = VK['action']['member_id'];
                if ($id == -GROUP) {
                    BotMessage::Send('П-приветик, друзяшки! Меня зовут Слада, можете звать меня просто Сладенькая :з<br>Я могу управлять вашей беседой. Также, в меня встроено немало развлекательных команд (Посмотреть их можно, командой -- Sweety, Помощь 2. Их можно отключить командой Sweety, ОтклРазвлекательные, и включить командой Sweety, ВклРазвлекательные)<br>Для того, чтобы я смогла вам помогать, дайте мне права администратора, и доступ ко всей переписке, и напишите "Sweety, установить" :-)');
                    break;
                } else {
                    $data = Account::GetDataUser($id);
                    if (!$data) {
                        break;
                    }
                    if ($data['ban']) {
                        BotConversation::DeleteUser($id);

                        $name = BotGet::Name(true, 'nom', $id);

                        $who = BotGet::Name(true, 'ins', $data['ban_by']);

                        if (!empty($data['ban_desc'])) {
                            $reason = "по причине {$data['ban_desc']}";
                        } else {
                            $reason = "";
                        }

                        BotMessage::Send("Пользователь @id{$id} ($name) забанен @id{$data['ban_by']} ({$who}) {$reason}<br>");
                    }
                }
                break;
            case 'chat_kick_user':
                $bool = Account::GetData()['antiLeave'];
                if ($bool) {
                    BotConversation::DeleteUser(VK['action']['member_id']);
                }
                break;
        }
        return;
    }
}