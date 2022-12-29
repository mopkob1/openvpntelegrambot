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
use loandbeholdru\shorts\arrays;

class NewvirtCommand extends AdminCommand
{
    use miscCommands;
    use miscBroadcast;
    use miscGetters;

    protected $name = 'newvirt';                      // Your command's name
    protected $description = msg::NEWVIRT_DESC; // Your command description
    protected $usage = '/newvirt [prefix]';                    // Usage of your command
    protected $version = '1.0.0';
    protected $need_mysql = false;


    public function execute(): ServerResponse
    {
        $info = [$userid = $this->chatid()];
        try {
            $this->chatAction($this->chatid())
                ->firstopt($prefix);

            $this->checkAdmins(...consts::ADMINS());
            $name = $this->vpnname($userid);
            $prefix = $prefix ?? $name ;

            $newvirt = sprintf("%s_%s", $prefix, hash(
                'crc32', sprintf("%s%s", $name, count($this->virts($userid)))));
            $this->virts[$newvirt] = ['created' => time()];
            local::adduser([$userid => $this->virts], consts::VIRTS());
            $msg[] = sprintf(msg::newvirt('DONE'), $newvirt, $name);
            $msg[] = sprintf("*/newuser %s*", $newvirt);
        }catch (\Throwable $e){
            $msg[] = $e->getMessage();
        }

        return $this->broadcast($msg, ...$info);
    }





}