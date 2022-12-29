<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\assets\consts;
use App\assets\msg;
use App\misc\local;
use App\misc\userNotfoundException;
use App\share\miscGetters;
use Helper\miscBroadcast;
use Helper\miscCommands;
use Helper\operconsts;
use Longman\TelegramBot\Commands\AdminCommand;
use Longman\TelegramBot\Entities\ServerResponse;

class RmvirtCommand extends AdminCommand
{
    use miscCommands;
    use miscBroadcast;
    use miscGetters;

    protected $name = 'rmvirt';                      // Your command's name
    protected $description = msg::RMVIRT_DESC; // Your command description
    protected $usage = '/rmvirt <virtname>';                    // Usage of your command
    protected $version = '1.0.0';
    protected $need_mysql = false;
    protected $virts = null;

    public function execute(): ServerResponse
    {

        $info = [$userid = $this->chatid()];
        try {
            $this->chatAction($this->chatid());
            $this->checkAdmins(...consts::ADMINS())
                ->firstopt($torm);

            list($torm, $name) = $this->lookforname($torm ?? $userid,
                new userNotfoundException(sprintf(
                    msg::rmvirt('NOTFOUND'), $torm, $this->vpnname($userid))));
            $this->virts($torm);

            $this->rmcerts($name);
            $this->virts[$name] = ['removed' => time()] + ($this->virts[$name] ?? []);
            local::adduser([$userid => $this->virts], consts::VIRTS());
            $msg[] = sprintf(msg::rmvirt('DONE'), $name, $this->vpnname($torm));

        }catch (\Throwable $e){
            $msg[] = $e->getMessage();
        }

        return $this->broadcast($msg, ...$info);
    }

    protected function rmcerts($name)
    {
        if(!local::checkeusers($name, consts::CERTsSTORE())) return  $this;
        $this->telegram->setCommandConfig('revoke', [
            operconsts::CHATID => $this->getMessage()->getChat()->getId(),
            operconsts::DEFTEXT => $name
        ]);
        $this->telegram->runCommands(['/revoke']);
        return $this;
    }




}