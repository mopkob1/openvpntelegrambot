<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\assets\consts;
use App\assets\msg;

use App\misc\local;

use App\share\miscGetters;
use Helper\miscBroadcast;
use Helper\miscCommands;
use loandbeholdru\pipe\bashCommandErrorException;
use loandbeholdru\pipe\brokenPipeException;
use loandbeholdru\pipe\pipecommand;
use loandbeholdru\pipe\piperesult;
use loandbeholdru\pipe\pipe;
use Longman\TelegramBot\Commands\AdminCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use loandbeholdru\shorts\arrays;


class UsersCommand extends AdminCommand
{
    use miscCommands;
    use miscBroadcast;
    use miscGetters;

    protected $name = 'users';                      // Your command's name
    protected $description = msg::USERS_DESC; // Your command description
    protected $usage = '/users';                    // Usage of your command
    protected $version = '1.0.0';
    protected $need_mysql = false;

    public function execute(): ServerResponse
    {
        try {
            $info = [$this->chatid()];

            $this->chatAction($info);

            $msg = $this->listing();

        }catch (\Throwable $e){
            $msg[] = $e->getMessage();
        }
        $msg = empty($msg) ? msg::users('EMPTY') : $msg;
        return $this->broadcast($msg , ...$info);
    }

    protected function listing()
    {
        $result = new piperesult(
            consts::FILES(), new brokenPipeException(msg::users('PIPE_ERROR')));
        $command = new pipecommand(consts::PIPE(), 'users');

        $resp = pipe::exec(
            $command, $result, new bashCommandErrorException(msg::users('USERS_ERROR')));

        $users = array_filter(explode("\n", str_replace("\n\n", "\n", $resp)));
        foreach ($users as $user)
            if (strpos($user, "#"))
                $disabled[] =  $this->formated($user);
            else
                $enabled[] = $this->formated($user);

        $ret1 = empty($enabled) ? ""
            : sprintf(msg::users('ENABLED'), implode("\n", array_filter($enabled ?? [])));
        $ret2 = empty($disabled) ? ""
            : sprintf(msg::users('DISABLED'), implode("\n", array_filter($disabled ?? [])));

        return array_filter([$ret1, $ret2]);
    }

    protected function formated(string $line)
    {
        $user = arrays::first(explode(":", $line));
        list($user, $name) = $this->lookforname($user);
        $line = str_replace(" ", "-", $line);
        $line = "*" . str_replace(":", "* ($user)\n", $line);
        $line = str_replace("#", "", $line);
        return "$line\n";
    }
}