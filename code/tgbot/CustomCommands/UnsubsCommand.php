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

class UnsubsCommand extends UserCommand
{
    use miscCommands;
    use miscGetters;
    use miscBroadcast;

    protected $name = 'unsubs';                      // Your command's name
    protected $description = msg::UNSUBS_DESC; // Your command description
    protected $usage = '/unsubs';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    public function execute() : ServerResponse
    {
        try {
            $tounsubs = $this->getMessage()
                ->getText(true);
            $tounsubs = empty($tounsubs)
                ? $this->chatid() : $tounsubs;

            $users = $this->userid($tounsubs)
                ->users(consts::SUBSSTORE());
            $newusers = array_diff($users, [$tounsubs]);

            if (count($users) === count($newusers)){
                $tounsubs = empty($tounsubs) ? msg::UNKNOWN : $tounsubs;
                throw new otherException(msg::unsubs('NOTFOUND'));
            }

            $this->storeusers($newusers, consts::SUBSSTORE());

            if ($tounsubs != $this->chatid())
                $this->sendMessage(msg::unsubs('DONE'), $tounsubs);

            $msg = sprintf(msg::unsubs('MSG'), $tounsubs);
        }catch (\Throwable $e){
            $msg = sprintf(msg::unsubs('ERROR'), $tounsubs, $e->getMessage());
        }
        return $this->sendMessage($msg);
    }

}