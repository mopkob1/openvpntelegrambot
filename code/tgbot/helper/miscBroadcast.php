<?php

namespace Helper;

use App\assets\consts;
use App\misc\miscInform;
use Longman\TelegramBot\Entities\PhotoSize;
use Longman\TelegramBot\Request;

trait miscBroadcast
{
    use miscCommands;
    use miscBotOperations;
    use miscInform;
    protected $caption;
    protected function broadcast($messages, int ...$ids)
    {
        $ids = array_unique($ids);
        $messages = is_array($messages)
            ? $messages : [$messages];
        foreach ($ids as $id)
            foreach ($messages as $message)
                $resp = $this->sendMessage($message, $id);

        return $resp;
    }
    protected function broadcastPhoto($caption,  PhotoSize $photos, int ...$chat_ids)
    {
        $photo = $photos->getFileId();
        $parse_mode = 'MARKDOWN';
        foreach ($chat_ids as $chat_id)
            $resp =  Request::sendPhoto(compact(
                'chat_id', 'photo', 'caption', 'parse_mode'
            ));
        return $resp;
    }
    protected function requestAdmin(string $userInform, string $adminInformTemplate, string $lastStrTemplate = "/hire %s")
    {

        extract($this->buildCaption($adminInformTemplate));
        $this->broadcast($userInform, $user_id);
        $photos = $this->chatAction($chat_id)
            ->getUserPhotos($user_id);

        if (empty($photos))
            $this->broadcast($adminInformTemplate, ...consts::ADMINS());
        else
            $this->broadcastPhoto(
                $adminInformTemplate, $photos[0][2], ...consts::ADMINS());
        return $this->broadcast(
            sprintf($lastStrTemplate, $user_id), ...consts::ADMINS());
    }


}