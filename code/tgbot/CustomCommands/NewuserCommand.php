<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\assets\consts;
use App\assets\msg;
use App\misc\local;
use App\misc\userNotfoundException;
use App\share\miscGetters;
use Helper\alreadyException;
use Helper\miscBroadcast;
use Helper\miscCommands;
use loandbeholdru\pipe\bashCommandErrorException;
use loandbeholdru\pipe\brokenPipeException;
use loandbeholdru\pipe\pipecommand;
use loandbeholdru\pipe\piperesult;
use Helper\notadminException;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;

class NewuserCommand extends UserCommand
{
    use miscCommands;
    use miscBroadcast;
    use miscGetters;

    protected $name = 'newuser';                      // Your command's name
    protected $description = msg::NEWUSER_DESC; // Your command description
    protected $usage = '/newuser [<name>]';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    protected $result;

    public function execute() : ServerResponse
    {
        $info = [$this->chatid()];
        try{
            $this->firstopt($tocert);

            list($tocert, $name) = $this->lookforname(
                $tocert, new userNotfoundException(sprintf(
                    msg::newuser('NOTREG'), $tocert)));
            $this->checkAdmins(...consts::ADMINS());
            $info[] =  $tocert;
            // Админ задал существующего пользователя. Реального или виртуального
        }catch (notadminException $e){
            // Если ты не админ, ты не можешь никого добавлять кроме себя

            $name = $this->vpnname($this->chatid());
            $changes = array_filter([
                empty($name) ? null : sprintf(msg::newuser('NORIGHTS1'), $name),
                empty($tocert) ? null : sprintf(msg::newuser('NORIGHTS2'),  $tocert, $name)
            ]);
            $this->broadcast($changes, $this->chatid());
            $tocert = $this->chatid();
        }catch (userNotfoundException $nf){
            // Админ, нет таких пользователей ни среди реальных ни среди виртуальных
            return $this->broadcast($nf->getMessage(), ...$info);
        }

        try {
            $this->chatAction($info);
            $info = array_unique($info);

            // Общий считыватель результатов для команд из pipe
            $this->result = new piperesult(
                consts::FILES(), new brokenPipeException(msg::newuser('PIPE_ERROR')));

            $this->checkUsers($name, consts::CERTsSTORE())
                ->checkAdmins(...consts::ADMINS());

            $msg[] = $this->newcert($tocert, $name);


        }catch (alreadyException $ae){
            // Если уже есть сертификат
            $resp = $this->broadcast(msg::newuser('ALREADY'), ...$info);

        }catch (notadminException $na){
            return empty($this->vpnname($tocert))
                ? $this->broadcast(sprintf(msg::newuser('NOTFOUND'), $this->name), $tocert)
                : $this->requestAdmin(
                    msg::newuser('REQUEST'),
                    msg::newuser('WANT_TO'),
                    msg::newuser('COM_TEMPLATE'));
        }catch (\Throwable $e){
            $msg[] = sprintf(msg::newuser('ERROR'), $tocert);
            $msg[] = $e->getMessage();
            return  $this->broadcast($msg, ...$info);
        }
        $this->chatAction($info);
        $msg[] = $this->uploadCerts($tocert, $name);

        return in_array($tocert, consts::ADMINS()) ?
            $resp : $this->broadcast($msg, ...consts::ADMINS());
    }

    protected function uploadCerts(int $userid, string $name)
    {
        $command = new pipecommand(consts::PIPE(), 'cert %s', $name);

        $resp = local::releaseCommand(
            $command, $this->result, new bashCommandErrorException(msg::newuser('GET_ERROR')));
        $file = sprintf("%s/%s", consts::CLIENTS(), $name);
        $this->sendFile("$file.zip", $userid, "Certs for computers.");
        $this->sendFile("$file.ovpn", $userid, "Certs for mobiles.");
        unlink("$file.zip");
        unlink("$file.ovpn");

        return sprintf(msg::newuser('DONE'), $name);
    }

    protected function newcert(int $userid, string $name)
    {

        $command = new pipecommand(consts::PIPE(), 'newuser %s', $name);

        $resp = local::releaseCommand(
            $command, $this->result, new bashCommandErrorException(msg::newuser('CREATE_ERROR')), 3);

        local::adduser($name, consts::CERTsSTORE());

        return sprintf(msg::newuser('CREATED'), $name);
    }

}