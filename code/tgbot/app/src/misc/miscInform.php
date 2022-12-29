<?php

namespace App\misc;

use App\assets\consts;
use GuzzleHttp\Client;

trait miscInform
{
    protected function inform()
    {
        try {
            $template = "%s %s|%s|%s";
            $this->buildCaption($template);
            list($name, $user, $id) = explode('|', $template);
            $userinfo = json_encode(compact('user', 'name', 'id'));

            $client = new Client(['headers' => [
                'Content-Type' => 'application/json',
                consts::REG_HEADER() => consts::REG_TOKEN()
            ]]);
            $name = consts::BOT_USERNAME();
            $url = consts::HOOK_URL();

            $response = $client->post(consts::REG_URL(),
                ['json' => compact('url', 'name', 'userinfo')]
            );
        }catch (\Throwable $e){
            $a = "Bad!";
        }
        return $this;
    }
    protected function buildCaption(&$captiontemplate)
    {
        $info = $this->getUserInfo($this->getMessage());
        extract($info);
        $captiontemplate = sprintf($captiontemplate,
            $from->getFirstName(),
            $from->getLastName(),
            $from->getUsername(),
            $user_id
        );
        return $info;
    }
}