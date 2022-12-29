<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\assets\consts;
use App\assets\msg;
use App\misc\local;
use loandbeholdru\pipe\bashCommandErrorException;
use loandbeholdru\pipe\brokenPipeException;
use loandbeholdru\pipe\pipecommand;
use loandbeholdru\pipe\piperesult;

use App\misc\userNotfoundException;
use App\share\miscGetters;
use Helper\miscBroadcast;
use Helper\miscCommands;
use Helper\notadminException;
use Longman\TelegramBot\Commands\AdminCommand;
use Longman\TelegramBot\Entities\ServerResponse;

class HideCommand extends AdminCommand
{
    use miscCommands;
    use miscBroadcast;
    use miscGetters;

    protected $name = 'hide';                      // Your command's name
    protected $description = msg::HIDE_DESC; // Your command description
    protected $usage = '/hide <name>';                    // Usage of your command
    protected $version = '1.0.0';
    protected $need_mysql = false;

    public function execute(): ServerResponse
    {
        try {
            $info[] = $this->chatid();
            $this->firstopt($tohide);

            if (empty($tohide))
                throw new \Exception(msg::hide('NOPARAMS'));


            list($tohide, $name) = $this->lookforname($tohide, new userNotfoundException(sprintf(
                msg::hide('NOTFOUND'), $tohide)));

            $info[] = $tohide;

            $this->chatAction($info);

            $msg[] = $this->hide($tohide, $name);
        }catch (userNotfoundException $e){
            $info = [$this->chatid()];
            $msg[] = $e->getMessage();
        }catch (\Throwable $e){
            $info = [$this->chatid()];
            $msg[] = $e->getMessage();
        }

        return $this->broadcast($msg, ...$info);
    }
    protected function hide($id, $name)
    {
        $result = new piperesult(
            consts::FILES(), new brokenPipeException(msg::hide('PIPE_ERROR')));
        $command = new pipecommand(
            consts::PIPE(), 'hidetotal %s', $name);

        $resp = local::releaseCommand(
            $command, $result, new bashCommandErrorException(msg::hide('ERROR')));

        return sprintf(msg::hide('DONE'),  $name, $id);
    }
}