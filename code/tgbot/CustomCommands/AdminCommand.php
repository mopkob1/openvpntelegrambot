<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\assets\consts;
use App\assets\msg;
use App\share\miscGetters;
use Helper\miscBroadcast;
use Helper\miscCommands;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;


class AdminCommand extends UserCommand
{
    use miscCommands;
    use miscBroadcast;
    use miscGetters;
    protected $name = 'admin';                      // Your command's name
    protected $description = msg::ADMIN_DESC; // Your command description
    protected $usage = '/admin message';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    public function execute() : ServerResponse
    {
        $message = $this->getMessage()
            ->getText(true);
        if (empty($message))
            return $this->broadcast(msg::admin("EMPTY"), $this->chatid());
        $msg[] = sprintf(msg::admin('LINE'), $this->vpnname($this->chatid()) ?? $this->chatid());
        $msg[] = $message;
        $msg[] = sprintf(msg::admin('REPLY'), $this->vpnname($this->chatid()) ?? $this->chatid());

        return $this->broadcast($msg, ...consts::ADMINS());

    }


}