<?php

namespace Helper;

use App\misc\local;
use Longman\TelegramBot\Request;
use loandbeholdru\shorts\arrays;

trait miscCommands
{
    use defaultchatid;
    protected function sendMessage( string $text = null, int $chatid = null)
    {
        $chat_id = $this->chatid($chatid);
        $text = $this->defaulttext($text);
        $parse_mode = 'MARKDOWN';
        return Request::sendMessage(compact(
            'chat_id', 'text', 'parse_mode'
        ));
    }

    protected function firstopt(&$opt = null, \Exception $e = null)
    {
        $opt = arrays::first(explode(" ", $this->getConfig(operconsts::DEFTEXT)
            ?? $this->getMessage()->getText(true)?? ""));
        if (empty($opt)){
            $opt = null;
            if (!empty($e)) throw $e;
        }

        return $this;
    }

    protected function sendFile(string $filename, int $chat_id = null, string $caption = null)
    {
        $chat_id = $this->chatid($chat_id);
        $caption = $caption ?? basename($filename);
        $document = Request::encodeFile($filename);
        return Request::sendDocument(compact(
            'document', 'caption', 'chat_id'
        ));
    }

    protected function chatAction($chat_ids, $action = 'typing', array $options = [])
    {
        $chat_ids = is_array($chat_ids) ? $chat_ids : [$chat_ids];
        foreach ($chat_ids as $chat_id)
            Request::sendChatAction(compact(
                    'chat_id', 'action') + $options);

        return $this;
    }

    protected function runCommands(string $chatid, string $text, string ...$noslashcommands)
    {
        $conf = [
            operconsts::CHATID => (int)$chatid,
            operconsts::DEFTEXT => $text
        ];
        $tg = $this->getTelegram();
        foreach ($noslashcommands as $command) {
            $tg->setCommandConfig($command, $conf);
            $commands[] = "/$command";
        }
        $tg->runCommands($commands ?? []);
        return $this;
    }

    protected function users(string $storepath)
    {
        return local::loadusers($storepath);
    }

    protected function checkAdmins(int ...$admins)
    {
        local::checkInList($this->chatid(), new notadminException(
            "You are not authorized to do '$this->name'!"),...$admins);
        return $this;
    }

    protected function checkUsers($useridORname, string $store = null)
    {
        if (local::checkeusers($useridORname, $store))
            throw new alreadyException();
        return $this;
    }

    protected function storeusers(array $users, string $storepath)
    {
        local::storeusers($users, $storepath);
        return $this;
    }


}