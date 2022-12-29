<?php

namespace App\other;

use App\assets\consts;

use Helper\miscBots;
use Helper\operconsts;
use Longman\TelegramBot\Telegram;
use loandbeholdru\slimcontrol\api\controllerApi;
use loandbeholdru\slimcontrol\middlewares\authException;


class telegramResend extends controllerApi
{
    use miscBots;

    /** @var Telegram */
    protected $tg;

    protected function process()
    {

        $this->assertCall()
            ->initbot()
            ->buildMessage($text)
            ->stat([operconsts::DEFTEXT => $text])
            ->clear();

        foreach ($this->users() as $user)
            $this->send($user, $text);

        die('ok!!!!');
    }

    protected function initbot()
    {
        $this->tg = require __DIR__ . '/../../../botsetup.php';
        return $this;
    }

    protected function buildMessage(string &$text = null)
    {
        throw new \Exception(sprintf(
            "You have to implement method: %s in class: %s",
            __CLASS__, __METHOD__));
    }

    protected function users()
    {
        throw new \Exception(sprintf(
            "You have to implement method: %s in class: %s",
            __CLASS__, __METHOD__));
    }

    protected function assertCall()
    {
        if ($this->args[consts::API_KEY_NAME()] !== consts::API_KEY_VAL())
            throw new authException("You are not authorized");
        return $this;
    }

    protected function ins()
    {
        throw new \Exception(sprintf(
            "You have to implement method: %s in class: %s",
            __CLASS__, __METHOD__));
    }

    protected function asss()
    {
        throw new \Exception(sprintf(
            "You have to implement method: %s in class: %s",
            __CLASS__, __METHOD__));
    }

}