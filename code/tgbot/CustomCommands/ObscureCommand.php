<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\assets\consts;
use App\assets\msg;
use App\misc\local;
use App\misc\userNotfoundException;
use App\share\miscGetters;
use Helper\miscBroadcast;
use Helper\miscCommands;
use Longman\TelegramBot\Commands\AdminCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use loandbeholdru\pipe\bashCommandErrorException;
use loandbeholdru\pipe\brokenPipeException;
use loandbeholdru\pipe\pipecommand;
use loandbeholdru\pipe\piperesult;


class ObscureCommand extends AdminCommand
{
    use miscCommands;
    use miscBroadcast;
    use miscGetters;

    protected $name = 'obscure';                      // Your command's name
    protected $description = msg::OBSCURE_DESC; // Your command description
    protected $usage = '/obscure <public> <individual>';                    // Usage of your command
    protected $version = '1.0.0';
    protected $need_mysql = false;

    public function execute(): ServerResponse
    {
        try {
            $info[] = $this->chatid();
            $toobscure = $this->getMessage()
                ->getText(true);
            $toobscure = array_filter(explode(' ', $toobscure));

            if (empty($toobscure) or (count($toobscure) < 2))
                throw new \Exception(msg::obscure('NOPARAMS'));
            list($user1, $user2) = $toobscure;

            list($user1, $name1) = $this->lookforname($user1, new userNotfoundException(sprintf(
                msg::obscure('NOTFOUND'), $user1)));

            list($user2, $name2) = $this->lookforname($user2, new userNotfoundException(sprintf(
                    msg::obscure('NOTFOUND'), $user2)));

            $info = array_merge($info, [$user2, $user1]);

            $this->chatAction($info);

            $msg[] = $this->obscure(compact(
                'user2', 'user1', 'name1', 'name2'));

        }catch (userNotfoundException $e){
            $info = [$this->chatid()];
            $msg[] = $e->getMessage();
        }catch (\Throwable $e){
            $msg[] = $e->getMessage();
            $info = [$this->chatid()];
        }

        return $this->broadcast($msg, ...$info);
    }
    protected function obscure(array $data)
    {
        extract($data);
        $result = new piperesult(
            consts::FILES(), new brokenPipeException(msg::obscure('PIPE_ERROR')));
        $command = new pipecommand(
            consts::PIPE(), 'hidefrom %s %s', $name1, $name2);

        try{
            $resp = local::releaseCommand(
                $command, $result, new bashCommandErrorException(msg::obscure('ERROR')));
        }catch (bashCommandErrorException $e){
            if ($e->getMessage() === "You have to give public host in first place\n")
                throw new bashCommandErrorException(msg::obscure('PUB_ERROR'));
            else
                throw $e;
        }

        return sprintf(msg::obscure('DONE'),
            $name2, $user2, $name1, $user1,
        );
    }
}