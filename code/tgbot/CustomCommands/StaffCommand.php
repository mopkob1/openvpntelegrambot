<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\assets\consts;
use App\assets\msg;
use App\misc\bashCommandErrorException;
use App\misc\brokenPipeException;
use App\misc\local;
use App\misc\pipecommand;
use App\misc\piperesult;
use App\share\miscGetters;
use Helper\miscBroadcast;
use Helper\miscCommands;
use Longman\TelegramBot\Commands\AdminCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use loandbeholdru\shorts\arrays;

class StaffCommand extends AdminCommand
{
    use miscCommands;
    use miscBroadcast;
    use miscGetters;

    protected $name = 'staff';                      // Your command's name
    protected $description = msg::STAFF_DESC; // Your command description
    protected $usage = '/staff';                    // Usage of your command
    protected $version = '1.0.0';
    protected $need_mysql = false;

    public function execute(): ServerResponse
    {
        try {
            $info = [$this->chatid()];
            $this->chatAction($info);

            $a[] = msg::staff('ADM_HEADER');

            foreach (consts::ADMINS() as $ADMIN)
                $a[] = $this->formatter('USER', $ADMIN);
            
            $users = local::loadusers(consts::USERsSTORE());
            $subs = local::loadusers(consts::SUBSSTORE());

            $candidates = array_diff_key($this->vpnusers ?? [], array_flip($users ?? []));
            foreach ($candidates as $id => $candidate)
                $c = array_merge(
                    $c ?? [msg::staff('CAND_HEaDER')],
                    [$this->formatter('USER', $id)]);

            $u[] = empty($users) ?
                msg::staff('NO_USERS') : msg::staff('USER_HEADER');
            foreach ($users as $user) {
                $s = in_array((int)$user, $subs)
                    ? 'USER_SUBS': 'USER_UNSUBS';
                $u[] = $this->formatter($s, $user);
            }



            $msg[] = implode("\n", $a ?? []) . "\n";
            $msg[] = implode("\n", $c ?? []) . "\n";
            $msg[] = implode("\n", $u ?? []) . "\n";

        }catch (\Throwable $e){
            $msg[] = $e->getMessage();
        }

        return $this->broadcast($msg, ...$info);
    }

    protected function formatter($const, $name)
    {
        return sprintf(msg::staff($const), $this->vpnname($name) ?? $name);
    }



}