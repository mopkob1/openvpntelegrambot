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
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Commands\AdminCommand;

class ImpartCommand extends AdminCommand
{
    use miscCommands;
    use miscBroadcast;
    use miscGetters;

    protected $name = 'impart';                      // Your command's name
    protected $description = msg::IMPART_DESC; // Your command description
    protected $usage = '/impart <cert> [<user>]';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command
    protected $need_mysql = false;
    protected $result;

    public function execute() : ServerResponse
    {
        try{
            $info[] = $this->chatid();
            $params = $this->getMessage()
                ->getText(true);
            $data = array_filter(explode(' ', $params));

            if (empty($data))
                throw new \Exception(msg::impart('NOPARAMS'));
            list($certs, $user) = $data;
            $user = empty($user) ? $this->chatid() : $user;
            list($user, $name) = $this->lookforname(
                $user, new userNotfoundException(sprintf(
                msg::impart('NOTREG'), $user)));
            if (local::loadusers(consts::PARTICIPANTS())["$user"] !== $name)
                throw new userNotfoundException(sprintf(
                    msg::impart('VIRT'), $name));
            $this->chatAction($info);
            $db = local::loadusers(consts::CERTsSTORE());
            $users = preg_grep(sprintf('/.*%s.*/', $certs), $db);


            if (empty($users))
                throw new userNotfoundException(sprintf(
                    msg::impart('NOCERTS'), $certs));
            $this->broadcast(msg::impart('INFORM'), $user);
            foreach ($users as $alias)
                $reply[] = $this->uploadCerts($user, $alias);

            // Админ задал существующего пользователя. Реального или виртуального

        }catch (userNotfoundException $nf){
            // Админ, нет таких пользователей ни среди реальных ни среди виртуальных
            return $this->broadcast($nf->getMessage(), ...$info);

        }catch (\Throwable $e){
            $msg[] = sprintf(msg::impart('ERROR'), $alias);
            $msg[] = $e->getMessage();
            return  $this->broadcast($msg, $user, ...$info);
        }
        return $this->broadcast($reply, ...$info);
    }

    protected function uploadCerts(int $userid, string $name)
    {
        $command = new pipecommand(consts::PIPE(), 'cert %s', $name);
        $result = new piperesult(
            consts::FILES(), new brokenPipeException(msg::impart('PIPE_ERROR')));
        $resp = local::releaseCommand(
            $command, $result, new bashCommandErrorException(msg::impart('GET_ERROR')));
        $file = sprintf("%s/%s", consts::CLIENTS(), $name);
        $this->sendFile("$file.zip", $userid, "Certs for computers.");
        $this->sendFile("$file.ovpn", $userid, "Certs for mobiles.");
        unlink("$file.zip");
        unlink("$file.ovpn");

        return sprintf(msg::impart('DONE'), $name);
    }





}