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
use Longman\TelegramBot\Commands\AdminCommand;
use Longman\TelegramBot\Entities\ServerResponse;

class PublicCommand extends AdminCommand
{
    use miscCommands;
    use miscBroadcast;
    use miscGetters;

    protected $name = 'public';                      // Your command's name
    protected $description = msg::PUBLIC_DESC; // Your command description
    protected $usage = '/public <name>';                    // Usage of your command
    protected $version = '1.0.0';
    protected $need_mysql = false;

    public function execute(): ServerResponse
    {
        try {
            $info[] = $this->chatid();
            $this->firstopt($topub);

            if (empty($topub))
                throw new \Exception(msg::public('NOPARAMS'));

            list($topub, $name) = $this->lookforname($topub, new userNotfoundException(sprintf(
                msg::public('NOTFOUND'), $topub)));

            $info[] = $topub;

            $this->chatAction($info);

            $msg[] = $this->public($topub, $name);
        }catch (userNotfoundException $e){
            $info = [$this->chatid()];
            $msg[] = $e->getMessage();
        }catch (\Throwable $e){
            $info = [$this->chatid()];
            $msg[] = $e->getMessage();
        }

        return $this->broadcast($msg, ...$info);
    }
    protected function public(int $userid1, string $name)
    {
        $result = new piperesult(
            consts::FILES(), new brokenPipeException(msg::public('PIPE_ERROR')));
        $command = new pipecommand(
            consts::PIPE(), 'publicate %s', $name);

        $resp = local::releaseCommand(
            $command, $result, new bashCommandErrorException(msg::public('ERROR')));

//        local::deluser($this->vpnname($userid), consts::CERTsSTORE());

        return sprintf(msg::public('DONE'),  $name, $userid1);
    }
}