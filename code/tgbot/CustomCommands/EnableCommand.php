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

class EnableCommand extends AdminCommand
{
    use miscCommands;
    use miscBroadcast;
    use miscGetters;

    protected $name = 'enable';                      // Your command's name
    protected $description = msg::ENABLE_DESC; // Your command description
    protected $usage = '/enable [<name>]';                    // Usage of your command
    protected $version = '1.0.0';
    protected $need_mysql = false;

    public function execute(): ServerResponse
    {
        try {
            $this->firstopt($toenable);

            if (!empty($toenable)){
                $info[] = $this->chatid();
                $this->checkAdmins(...consts::ADMINS());
            }
            
            list($toenable, $name) = $this->lookforname($toenable, new userNotfoundException(sprintf(
                msg::enable('NOTFOUND'), $toenable)));

            $info = in_array($toenable, $info ?? [])
                ? $info : array_merge($info ?? [], [$toenable]);

            $this->chatAction($info);

            $msg[] = $this->enable($name);
        }catch (userNotfoundException $e){
            $msg[] = $e->getMessage();
            $info = [$this->chatid()];
        }catch (notadminException $na){
            $info = [$this->chatid()];
            $msg[] = sprintf(msg::enable('NORIGHTS'), $this->vpnname($toenable));
        }catch (\Throwable $e){
            $info = [$this->chatid()];
            $msg[] = $e->getMessage();
        }

        return $this->broadcast($msg, ...$info);
    }
    protected function enable($name)
    {
        $result = new piperesult(
            consts::FILES(), new brokenPipeException(msg::enable('PIPE_ERROR')));
        $command = new pipecommand(consts::PIPE(), 'enable %s', $name);

        $resp = local::releaseCommand(
            $command, $result, new bashCommandErrorException(msg::enable('EN_ERROR')));

//        local::deluser($this->vpnname($userid), consts::CERTsSTORE());

        return sprintf(msg::enable('DONE'), $name);
    }
}