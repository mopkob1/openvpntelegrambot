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

class DisableCommand extends AdminCommand
{
    use miscCommands;
    use miscBroadcast;
    use miscGetters;

    protected $name = 'disable';                      // Your command's name
    protected $description = msg::DISABLE_DESC; // Your command description
    protected $usage = '/disable [<name>]';                    // Usage of your command
    protected $version = '1.0.0';
    protected $need_mysql = false;

    public function execute(): ServerResponse
    {
        try {
            $this->firstopt($todisable);

            if (!empty($todisable)){
                $info[] = $this->chatid();
                $this->checkAdmins(...consts::ADMINS());
            }

            list($todisable, $name) = $this->lookforname($todisable, new userNotfoundException(sprintf(
                msg::public('NOTFOUND'), $todisable)));

            $info = in_array($todisable, $info ?? [])
                ? $info : array_merge($info ?? [], [$todisable]);

            $this->chatAction($info);

            $msg[] = $this->disable($name);
        }catch (userNotfoundException $e){
            $info = [$this->chatid()];
            $msg[] = $e->getMessage();

        }catch (notadminException $na){
            $info = [$this->chatid()];
            $msg[] = sprintf(msg::disable('NORIGHTS'), $this->vpnname($todisable));
        }catch (\Throwable $e){
            $info = [$this->chatid()];
            $msg[] = $e->getMessage();
        }

        return $this->broadcast($msg, ...$info);
    }
    protected function disable(string $name)
    {
        $result = new piperesult(
            consts::FILES(), new brokenPipeException(msg::disable('PIPE_ERROR')));
        $command = new pipecommand(consts::PIPE(), 'disable %s', $name);

        $resp = local::releaseCommand(
            $command, $result, new bashCommandErrorException(msg::disable('DIS_ERROR')));

//        local::deluser($this->vpnname($userid), consts::CERTsSTORE());

        return sprintf(msg::disable('DONE'), $name);
    }
}