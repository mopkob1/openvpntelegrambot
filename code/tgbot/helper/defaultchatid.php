<?php

namespace Helper;

use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

trait defaultchatid
{

    protected function chatid(int $chatid = null)
    {
        return $this->getConfig(operconsts::CHATID)
            ? $this->getConfig(operconsts::CHATID)
            : ($chatid ?? $this->getMessage()->getChat()->getId());
    }

    protected function defaulttext(string $defaulttext = null)
    {
        return $defaulttext ??
            $this->getConfig(operconsts::DEFTEXT) ?? "";
    }

    protected function localfile(string $file = null)
    {
        return $this->getConfig(operconsts::FILE)
            ? $this->getConfig(operconsts::FILE)
            : ($file ?? $this->getMessage()->getText(true));
    }

    // $types - один или несколько из: ['audio', 'document', 'photo', 'video', 'voice']
    protected function downloadLastFile(string ...$types)
    {
        $message = $this->getMessage();
        $message_type = $message->getType();

        if (in_array($message_type, $types, true)) {
            $doc = $message->{'get' . ucfirst($message_type)}();

            // For photos, get the best quality!
            ($message_type === 'photo') && $doc = end($doc);

            $file_id = $doc->getFileId();
            $file = Request::getFile(['file_id' => $file_id]);
            if ($file->isOk() && Request::downloadFile($file->getResult()))
                return $this->telegram->getDownloadPath() . '/' . $file->getResult()->getFilePath();
        }
        throw New TelegramException("Can't download last file!");
    }
    protected function reply(string $text = null)
    {
        $chat_id = $this->chatid();
        return empty($text)
            ? Request::emptyResponse()
            : Request::sendMessage(compact('chat_id', 'text'));
    }

}