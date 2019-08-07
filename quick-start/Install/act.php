<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('ROOT', $_SERVER['DOCUMENT_ROOT']);

function Api($method, $data, $token)
{
    $data = http_build_query(array_merge($data, ['access_token' => $token, 'v' => 5.81]));

    $response = file_get_contents('https://api.vk.com/method/' . $method . '?' . $data);

    return json_decode($response, true);
}

function rm($path)
{
    if (is_file($path)) return unlink($path);
    if (is_dir($path)) {
        foreach (scandir($path) as $p) if (($p != '.') && ($p != '..'))
            rm($path . DIRECTORY_SEPARATOR . $p);
        return rmdir($path);
    }
    return false;
}

$act = isset($_GET['do']) ? $_GET['do'] : '';

switch ($act) {
    case 'access':
        $token = isset($_POST['token']) ? $_POST['token'] : '';

        $response = Api('groups.getTokenPermissions', [], $token);

        if (isset($response['error'])) {
            header('Location: index.php?error=Неверный%20токен&path=index');
            exit();
        }

        if ($response['response']['mask'] < 405508) {
            header('Location: index.php?error=Токену%20разрешено%20не%20все&path=index');
            exit();
        }

        $id = Api('groups.getById', [], $token)['response'][0]['id'];

        file_put_contents('token.txt', $token);
        file_put_contents('id.txt', $id);
        file_put_contents('secret.txt', $_POST['secret']);

        header('Location: act.php?do=callback');
        break;
    case 'callback':
        $token = file_get_contents('token.txt');
        $id = file_get_contents('id.txt');

        $api = Api('groups.getCallbackConfirmationCode', ['group_id' => $id], $token);

        $confirm_code = $api['response']['code'];

        file_put_contents('confirm.txt', $confirm_code);

        header('Location: act.php?do=download');
        break;
    case 'download':
        require_once 'Zip.php';
        $github = file_get_contents('https://github.com/AntonShvets0/MVCBot/archive/master.zip');
        file_put_contents('bot.zip', $github);
        $zip = new Zip();
        $res = $zip->open('bot.zip');
        $zip->extractSubdirTo(ROOT, 'MVCBot-master/src');
        $zip->close();
        unlink(ROOT . '/.htaccess');
        unlink('bot.zip');
        $array = [
            'vkCallbackData' => ['secret' => file_get_contents('secret.txt'), 'confirm' => file_get_contents('confirm.txt')],
            'vkSendData' => ['v' => 5.81, 'access_token' => file_get_contents('token.txt')],
            'data' => ['errorChar' => '@', 'unknownCommand' => 'ОШИБКА: Неизвестная команда "{command}"', 'error' => 'ОШИБКА: {error}']
        ];
        $json = json_encode($array, JSON_UNESCAPED_UNICODE);
        $code = <<<PHP
<?php

Handler::Register('mvc-php', function () {
    return 'quick start: ok';
});

PHP;


        file_put_contents(ROOT . '/Config/config.json', $json);
        file_put_contents(ROOT . '/Config/Command.php', $code);
        header('Location: index.php?path=ok');
        break;
    case 'config':
        $token = file_get_contents('token.txt');
        $group = file_get_contents('id.txt');
        $secret = file_get_contents('secret.txt');

        $htaccess = <<<HTACCESS
RewriteEngine on
RewriteRule .* Controller.php
HTACCESS;

        file_put_contents(ROOT . '/.htaccess', $htaccess);
        rm(ROOT . '/Install');
        $server = Api('groups.addCallbackServer', ['group_id' => $group, 'url' => 'http://' . $_SERVER['HTTP_HOST'] . '/mvc', 'title' => 'MVCBot', 'secret_key' => $secret], $token)['response']['server_id'];

        Api('groups.setCallbackSettings', ['group_id' => $group, 'server_id' => $server, 'api_version' => '5.80', 'message_new' => 1], $token);

        echo 'Все прошло успешно. Если папка Install не удалилась -- удалите ее. Для проверки бота, напишите ему mvc-php';

        break;
    default:
        header('location: index.php');
}