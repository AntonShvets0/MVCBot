<?php

require_once ROOT . '/Models/VK/BotRequest.php';
require_once ROOT . '/Models/Utils.php';

class BotWall
{
    /**
     * @param int $postID
     * @param string $text
     * @param string|int $ownerID
     * @param array $attach
     * @param int $replyTo
     * @param int $stickerId
     * @return void
     */
    public static function AddComment($postID, $text, $ownerID = 'callback', $attach = [], $replyTo = 0, $stickerId = 0)
    {
        if ($ownerID == 'callback') {
            $ownerID = GROUP;
        }

        $data = [
            'owner_id' => $ownerID,
            'post_id' => $postID,
            'message' => $text,
            'attachments' => Utils::Join($attach)
        ];

        if (!empty($replyTo)) {
            $data['reply_to_comment'] = $replyTo;
        }

        if (!empty($stickerId)) {
            $data['sticker_id'] = $stickerId;
        }


        BotRequest::API('wall.createComment', $data);
    }

    /**
     * @param int $postID
     * @param string $type
     * @return void
     */
    public static function Comment($postID, $type = 'close')
    {
        $ownerID = GROUP;
        $type = $type == 'close' || $type == 'open' ? $type : 'close';

        BotRequest::API('wall.' . $type . 'Comments', ['owner_id' => $ownerID, 'post_id' => $postID]);
    }

}