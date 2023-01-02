<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\assets\consts;
use App\assets\msg;
use App\share\miscGetters;
use Helper\defaultchatid;
use Helper\miscBroadcast;
use Helper\miscCommands;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use loandbeholdru\slimcontrol\api\otherException;
use loandbeholdru\shorts\arrays;

class SubsCommand extends UserCommand
{
    use miscCommands;
    use miscGetters;
    use miscBroadcast;

    protected $name = 'subs';                      // Your command's name
    protected $description = msg::SUBS_DESC; // Your command description
    protected $usage = '/subs';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    public function execute() : ServerResponse
    {
        try {
            $tosubs = $this->getMessage()
                ->getText(true);
            $tosubs = empty($tosubs) ? $this->chatid() : $tosubs;


            $hired = $this->userid($tosubs)
                ->users(consts::USERsSTORE());

            if (!in_array($tosubs, $hired))
                throw new otherException(msg::subs('NOTFOUND'));

            $users = array_merge($this->users(consts::SUBSSTORE()), [$tosubs]);
            $users = array_unique($users);

            $this->storeusers($users, consts::SUBSSTORE());

            if ($tosubs != $this->chatid())
                $this->sendMessage(msg::subs('DONE'), $tosubs);

            $msg = sprintf(msg::subs('MSG'), $this->vpnname($tosubs));

        }catch (\Throwable $e){
            $msg = sprintf(msg::subs('ERROR'), $this->vpnname($tosubs), $e->getMessage());
        }

        return $this->broadcast($msg, ...consts::ADMINS());
    }

}