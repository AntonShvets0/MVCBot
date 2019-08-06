<?php

require_once ROOT . '/Models/VK/BotGet.php';

class BotUploader
{
    public static function Message($path = "", $peerID = 'callback')
    {
        if ($peerID == 'callback') {
            $peerID = BotGet::Peer();
        }
        $link = self::GetUploadMessageLink($peerID);

        $data = BotRequest::SendPostFile($link, ['photo' => self::GetFile($path)]);

        $photo = BotRequest::API('photos.saveMessagesPhoto', ['photo' => $data['photo'], 'server' => $data['server'], 'hash' => $data['hash']]);

        return 'photo' . $photo[0]['owner_id'] . '_' . $photo[0]['id'] . '_' . $photo[0]['access_key'];
    }

    public static function GetUploadMessageLink($peerID)
    {
        return BotRequest::API('photos.getMessagesUploadServer', ['peer_id' => $peerID])['upload_url'];
    }

    public static function GetFile($path)
    {
        return new CURLFile($path, mime_content_type($path), $path);
    }
}