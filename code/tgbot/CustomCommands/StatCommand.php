<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\assets\consts;
use App\assets\msg;
use Helper\miscBots;
use Helper\miscCommands;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Commands\AdminCommand;

use loandbeholdru\shorts\arrays;

class StatCommand extends AdminCommand
{
    use miscBots;
    use miscCommands;

    protected $name = 'stat';                      // Your command's name
    protected $description = msg::STAT_DESC; // Your command description
    protected $usage = '/stat [yesterday]';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    public function execute() : ServerResponse
    {
        $when = empty($this->getMessage()->getText(true))
            ? 'today' : 'yesterday';

        try {
            $this->checkAdmins(...consts::ADMINS());
            $msg = sprintf(msg::stat('MSG'), $when, $this->count());
        }catch (\Throwable $e){
            $msg = sprintf(msg::stat('ERROR'), $e->getMessage());
        }
        return $this->sendMessage($msg);
    }

    protected function count()
    {
        $file = empty($this->getMessage()->getText(true))
            ? $this->statfile()
            : $this->statfile(date("Y-m-d", strtotime( '-1 days' )));
        $json = file_get_contents($file);
        $data = arrays::valid_json($json, true, []);

        return "" . count(array_keys($data));
    }
}