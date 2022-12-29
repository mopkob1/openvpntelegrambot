<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\assets\consts;
use App\assets\msg;
use loandbeholdru\pipe\bashCommandErrorException;
use loandbeholdru\pipe\brokenPipeException;
use loandbeholdru\pipe\pipecommand;
use loandbeholdru\pipe\piperesult;

use App\misc\local;

use App\misc\userNotfoundException;
use App\share\miscGetters;
use Helper\miscBroadcast;
use Helper\miscCommands;
use Helper\notadminException;
use Longman\TelegramBot\Commands\AdminCommand;
use Longman\TelegramBot\Entities\ServerResponse;

class ConnectCommand extends AdminCommand
{
    use miscCommands;
    use miscBroadcast;
    use miscGetters;

    protected $name = 'connect';                      // Your command's name
    protected $description = msg::CONNECT_DESC; // Your command description
    protected $usage = '/connect <user1> <user2>';                    // Usage of your command
    protected $version = '1.0.0';
    protected $need_mysql = false;

    public function execute(): ServerResponse
    {
        try {
            $info[] = $this->chatid();
            $connecting = $this->getMessage()
                ->getText(true);
            $connecting = array_filter(explode(' ', $connecting));

            if (empty($connecting) or (count($connecting) < 2))
                throw new \Exception(msg::connect('NOPARAMS'));
            list($user1, $user2) = $connecting;

            list($user1, $name1) = $this->lookforname($user1, new userNotfoundException(sprintf(
                msg::connect('NOTFOUND'), $user1)));
            list($user2, $name2) = $this->lookforname($user2, new userNotfoundException(sprintf(
                    msg::connect('NOTFOUND'), $user2)));

            $info = array_merge($info, [$user2, $user1]);

            $this->chatAction($info);

            $msg[] = $this->connect(compact('user1', 'user2', 'name1', 'name2'));
        }catch (userNotfoundException $e){
            $msg[] = $e->getMessage();
        }catch (\Throwable $e){
            $msg[] = $e->getMessage();
        }

        return $this->broadcast($msg, ...$info);
    }
    protected function connect(array $data)
    {
        extract($data);
        $result = new piperesult(
            consts::FILES(), new brokenPipeException(msg::connect('PIPE_ERROR')));
        $command = new pipecommand(
            consts::PIPE(), 'bidirectional %s %s', $name1, $name2);

        $resp = local::releaseCommand(
            $command, $result, new bashCommandErrorException(msg::connect('ERROR')));



        return sprintf(msg::connect('DONE'),
            $name1, $user1, $name2, $user2);
    }
}