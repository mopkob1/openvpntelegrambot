<?php

namespace Helper;

use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\PhotoSize;
use Longman\TelegramBot\Entities\UserProfilePhotos;
use Longman\TelegramBot\Request;

trait miscBotOperations
{
    protected function getUserInfo(Message $message)
    {
        $from       = $message->getFrom();
        $user_id    = $from->getId();
        $chat_id    = $message->getChat()->getId();
        $message_id = $message->getMessageId();
        return compact(
            'from', 'user_id', 'chat_id', 'message_id');
    }



    protected function getUserPhotos($user_id, $limit = 10, $offset = null)
    {
        $response = Request::getUserProfilePhotos(compact(
            'user_id', 'limit', 'offset'
        ));
        if ($response->isOk()) {
            /** @var UserProfilePhotos $user_profile_photos */
            $user_profile_photos = $response->getResult();
            return $user_profile_photos->getTotalCount() > 0
                ? $user_profile_photos->getPhotos() : [];
        }
        return [];
    }



}