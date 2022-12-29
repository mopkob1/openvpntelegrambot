<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\assets\consts;
use App\assets\msg;
use App\share\miscGetters;
use Helper\miscBroadcast;
use Helper\miscCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use loandbeholdru\slimcontrol\api\otherException;


class FireCommand extends UserCommand
{
    use miscCommands;
    use miscBroadcast;
    use miscGetters;

    protected $name = 'fire';                      // Your command's name
    protected $description = msg::FIRE_DESC; // Your command description
    protected $usage = '/fire <telegram-id>';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    public function execute() : ServerResponse
    {
        try {
            $tofire = $this->getMessage()
                ->getText(true);
            $users = $this->userid($tofire)
                ->users(consts::USERsSTORE());
            $newusers = array_diff($users, [$tofire]);

            if (empty($tofire) || count($users) === count($newusers)) {
                $tofire = empty($tofire) ?  msg::UNKNOWN : $tofire;
                throw new otherException(msg::fire('NOTFOUND'));
            }

            $this->checkAdmins(...consts::ADMINS())
                ->runCommands($tofire, $tofire, 'unsubs')
                ->storeusers($newusers, consts::USERsSTORE());

            $this->sendMessage(msg::fire('DONE'), $tofire);
            $msg = sprintf(msg::fire('MSG'), $tofire);


        }catch (\Throwable $e){
            $msg = sprintf(msg::fire('ERROR'), $tofire, $e->getMessage());
        }
        return $this->broadcast($msg, ...consts::ADMINS());
    }


}