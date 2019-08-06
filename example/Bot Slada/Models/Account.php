<?php

require_once ROOT . '/Models/VK/BotGet.php';
require_once ROOT . '/Models/Sql/Sql.php';

class Account
{
    /**
     * @param string|int $id
     * Проверяет на предупреждения пользователя
     */
    public static function CheckWarn($id = 'callback')
    {
        if ($id == 'callback') {
            $id = BotGet::From();
        }

        $data = self::GetDataUser($id);
        if ($data && $data['warn'] >= 3) {
            $response = BotConversation::DeleteUser($id);

            if ($response) {
                Account::SetRow($id, ['ban' => 1, 'ban_desc' => '3 предупреждения', 'ban_by' => $id]);

                $name = BotGet::Name(true, 'nom', $id);

                BotMessage::Send("Пользователь @id{$id} ($name) забанен");
            } else {
                $name = BotGet::Name(true, 'gen', $id);

                BotMessage::Send("Не удалось забанить @id{$id} ($name)");
                Account::SetRow($id, ['warn' => 0, 'mute' => 0]);
            }
        }
    }

    /**
     * @param $id
     * @param $row
     * Устанавливает значение в бд для пользователя. Если пользователя нет -- создает
     */
    public static function SetRow($id, $row)
    {
        $data = self::GetDataUser($id);
        if ($data == false) {
            $row['user'] = $id;
            $row['conversation'] = BotGet::Peer();
            Sql::Insert($row)->From('users')->Exec();
        } else {
            Sql::Update($row)->From('users')->Where('user', $id)->Where('conversation', BotGet::Peer())->Exec();
        }
    }

    /**
     * @param $name
     * @return int
     * Пытается получить ID человека
     */
    public static function GetID($name)
    {
        if (is_int($name)) {
            $firstAndLastName = BotGet::Name(true, 'nom', $name);
            return $firstAndLastName == false ? 0 : $name;
        } elseif (mb_substr($name, 0, 2) == 'id') {
            $name = mb_substr($name, 2);
            $firstAndLastName = BotGet::Name(true, 'nom', $name);
            return $firstAndLastName == false ? 0 : $name;
        } elseif (mb_substr($name, 0, 3) == '[id') {
            $name = mb_substr($name, 3);
            list($name) = explode('|', $name);
            $firstAndLastName = BotGet::Name(true, 'nom', $name);
            return $firstAndLastName == false ? 0 : $name;
        }

        $code = 'return API.users.get({"user_ids": API.messages.getConversationMembers({"peer_id":' . BotGet::Peer() . '}).items@.member_id});';
        $api = BotRequest::API('execute', ['code' => $code]);

        foreach ($api as $array) {
            $nameArray = $array['first_name'] . ' ' . $array['last_name'];
            if (stripos($nameArray, $name) !== false) {
                return $array['id'];
            }
        }

        return 0;
    }

    /**
     * @param string $user_id
     * @return bool
     * Возвращает информацию о пользователе
     */
    public static function GetDataUser($user_id = 'callback')
    {
        if ($user_id == 'callback') {
            $user_id = BotGet::From();
        }
        $data = Sql::Select()->From('users')->Where('conversation', BotGet::Peer())->Where('user', $user_id)->Exec();
        return isset($data[0]) ? $data[0] : false;
    }

    /**
     * @param string $user_id
     * @return int
     * Польчает ранг пользователя
     */
    public static function GetRank($user_id = 'callback')
    {
        $data = self::GetDataUser($user_id);
        return isset($data['rank']) ? $data['rank'] : 0;
    }

    /**
     * @param $rank
     * @return mixed
     * Получает имя ранга
     */
    public static function GetRankName($rank)
    {
        $data = self::GetData();
        return json_decode($data['ranks'], true)[$rank];
    }

    /**
     * @return bool|array
     * Получает информацию о боте
     */
    public static function GetData()
    {
        $data = Sql::Select()->From('conversations')->Where('id', BotGet::Peer())->Exec();

        return isset($data[0]) ? $data[0] : false;
    }

    /**
     * @return string
     * Возвращает строку в зависимости от пола
     */
    public static function Gender()
    {
        $gender = BotGet::Gender();
        return $gender == 1 ? 'Братик' : 'Сестренка';
    }

    public static function OwoGender()
    {
        $gender = BotGet::Gender();
        return $gender == 1 ? 'Б-братик' : 'С-сестренка';
    }

    /**
     * @return bool
     * Проверяет на наличие в бд беседы
     */
    public static function Registered()
    {
        return self::GetData() != false;
    }

    /**
     * @return bool
     * Проверка. Беседа ли это
     */
    public static function IsConversation()
    {
        return BotGet::Peer() != BotGet::From();
    }
}