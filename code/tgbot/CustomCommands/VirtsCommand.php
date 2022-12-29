<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\assets\consts;
use App\assets\msg;
use App\misc\bashCommandErrorException;
use App\misc\brokenPipeException;
use App\misc\local;
use App\misc\pipecommand;
use App\misc\piperesult;
use App\misc\userNotfoundException;
use App\share\miscGetters;
use Helper\miscBroadcast;
use Helper\miscCommands;
use Helper\notadminException;
use Longman\TelegramBot\Commands\AdminCommand;
use Longman\TelegramBot\Entities\ServerResponse;

class VirtsCommand extends AdminCommand
{
    use miscCommands;
    use miscBroadcast;
    use miscGetters;

    protected $name = 'virts';                      // Your command's name
    protected $description = msg::VIRTS_DESC; // Your command description
    protected $usage = '/virts';                    // Usage of your command
    protected $version = '1.0.0';
    protected $need_mysql = false;

    public function execute(): ServerResponse
    {
        $info = [$userid = $this->chatid()];
        try {
            $this->chatAction($this->chatid());
            $this->checkAdmins(...consts::ADMINS());
            $name = $this->vpnname($userid);

            foreach ($this->virts($userid) as $virt => $data)
                if (empty($data['removed']))
                    $msg[] = sprintf(
                        msg::virts('LINE'), $virt, date("d-m-y", $data['created']));

            if (empty($msg))
                throw new userNotfoundException(sprintf(
                    msg::virts('NOTFOUND'),  $name
                ));

            array_unshift($msg, sprintf(
                msg::virts('DONE'), $name));

        }catch (\Throwable $e){
            $msg[] = $e->getMessage();
        }

        return $this->broadcast($msg, ...$info);
    }




}