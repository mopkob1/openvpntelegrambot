<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\assets\consts;
use App\assets\msg;
use App\misc\local;
use App\misc\userNotfoundException;
use App\share\miscGetters;
use Helper\miscBroadcast;
use Helper\miscCommands;

use Helper\notadminException;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use loandbeholdru\pipe\bashCommandErrorException;
use loandbeholdru\pipe\brokenPipeException;
use loandbeholdru\pipe\pipecommand;
use loandbeholdru\pipe\piperesult;

class RevokeCommand extends UserCommand
{
    use miscCommands;
    use miscBroadcast;
    use miscGetters;

    protected $name = 'revoke';                      // Your command's name
    protected $description = msg::REVOKE_DESC; // Your command description
    protected $usage = '/revoke [<name>]';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    protected $result;

    public function execute() : ServerResponse
    {
        $info = [$this->chatid()];
        try {
            $this->firstopt($torevoke);
            if (!empty($torevoke)){
                list($torevoke, $name) = $this->lookforname($torevoke);
                $info[] = $torevoke;
                $this->checkAdmins(...consts::ADMINS());
            }
            $info = array_filter(array_unique($info));

            $this->userid($torevoke, new userNotfoundException(sprintf(
                msg::revoke('NOTFOUND'), $torevoke ?? $name ?? "n|a")));
            $name = $name ?? $this->vpnname($torevoke) ?? $torevoke;
            local::checkeusers($name,consts::CERTsSTORE(),
                new userNotfoundException(sprintf(
                    msg::revoke('NOTFOUND'), $name  ?? "n|a")));

            $this->chatAction($info);

            $msg[] = $this->revoke($name);
        }catch (userNotfoundException $e){
            $msg[] = $e->getMessage();

        }catch (notadminException $na){
            $msg[] = sprintf(msg::revoke('NORIGHTS'), $name ?? $torevoke);
        }catch (\Throwable $e){
            $msg[] = $e->getMessage();
        }

        return $this->broadcast($msg, ...$info);
    }

    protected function revoke($name)
    {
        $result = new piperesult(
            consts::FILES(), new brokenPipeException(msg::revoke('PIPE_ERROR')));
        $command = new pipecommand(consts::PIPE(), 'revoke %s', $name);

        $resp = local::releaseCommand(
            $command, $result, new bashCommandErrorException(msg::revoke('DEL_ERROR')));

        local::deluser($name, consts::CERTsSTORE());

        return sprintf(msg::revoke('DONE'), $name);
    }

}