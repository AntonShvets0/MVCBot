<?php


class ControllerGame
{
    function ActionWho()
    {
        $text = Utils::Join(func_get_args(), ' ');
        $data = explode(PHP_EOL, file_get_contents(ROOT . '/Data/who.txt'));

        if (empty($text)) {
            return '@Правильный ввод: Sweety, swho <ТЕКСТ>';
        }

        $code = 'return API.users.get({"user_ids": API.messages.getConversationMembers({"peer_id":' . BotGet::Peer() . '}).items@.member_id});';
        $api = BotRequest::API('execute', ['code' => $code]);

        $rand = mt_rand(0, count($api) - 1);

        $people = "@id{$api[$rand]['id']} ({$api[$rand]['first_name']} {$api[$rand]['last_name']})";
        return str_replace(['{text}', '{people}'], [$text, $people], $data[mt_rand(0, count($data) - 1)]);
    }
}