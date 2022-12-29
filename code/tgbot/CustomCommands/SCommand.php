<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\assets\consts;
use App\assets\msg;
use App\misc\bashCommandErrorException;
use App\misc\brokenPipeException;
use App\misc\local;
use App\misc\pipecommand;
use App\misc\piperesult;
use App\misc\userNotfoundException;
use App\share\miscGetters;
use Helper\miscBroadcast;
use Helper\miscCommands;
use Helper\notadminException;
use Longman\TelegramBot\Commands\AdminCommand;
use Longman\TelegramBot\Entities\ServerResponse;

class SCommand extends AdminCommand
{
    use miscCommands;
    use miscBroadcast;
    use miscGetters;

    protected $name = 's';                      // Your command's name
    protected $description = msg::S_DESC; // Your command description
    protected $usage = '/s <name> <message>';                    // Usage of your command
    protected $version = '1.0.0';
    protected $need_mysql = false;

    public function execute(): ServerResponse
    {
        try {
            $message = array_filter(explode(" ", $this->getMessage()
                ->getText(true) ?? ""));
            $user = array_shift($message);
            if (empty($message))
                return $this->broadcast(msg::admin("EMPTY"), $this->chatid());


            $this->userid($user, new userNotfoundException(
                sprintf(msg::s('NOTFOUND'), $this->vpnname($user))));
            $info = array_diff(consts::ADMINS(), [$this->chatid()]);
            $info[] = $user;
            $msg[] = msg::s('LINE');
            $msg[] = implode(" ", $message);
        } catch (\Throwable $e) {
            $msg[] = $e->getMessage();
            $info[] = $this->chatid();
        }


        return $this->broadcast($msg, ...$info);
    }

}