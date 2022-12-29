<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\assets\msg;
use Helper\miscCommands;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;


class SendsmsCommand extends UserCommand
{
    use miscCommands;
    protected $name = 'sendsms';                      // Your command's name
    protected $description = msg::SENDSMS_DESC; // Your command description
    protected $usage = '/sendsms name message';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    public function execute() : ServerResponse
    {

        return $this->sendMessage();
    }


}