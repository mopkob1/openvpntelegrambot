<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\assets\consts;
use App\assets\msg;
use App\enrich\startMsg;
use Helper\miscBroadcast;
use Helper\operconsts;
use loandbeholdru\shorts\arrays;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;

class StartCommand extends UserCommand
{
    use startMsg;

    /**
     * @var string
     */
    protected $name = 'start';

    /**
     * @var string
     */
    protected $description = msg::START_DESC;

    /**
     * @var string
     */
    protected $usage = '/start';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * @var bool
     */
    protected $private_only = true;

    public function execute(): ServerResponse
    {
        try{
            $this->welcome($resp)
                ->hire($resp);
        }catch (\Throwable $e){
            return $this->broadcast($e->getMessage(), ...consts::ADMINS());
        }

        return $resp;
    }

    protected function hire(&$resp = null)
    {
        $this->telegram->setCommandConfig('hire', [
            operconsts::CHATID => $this->getMessage()->getChat()->getId()
        ]);
        $resp = $this->telegram->runCommands(['/hire'])[0];

        return $resp;
    }

    protected function welcome(&$resp)
    {
        consts::ENV("LOADINNOTYET");
        $msg = arrays::substfields(file_get_contents(consts::WELCOME()), $_ENV);
        $resp = $this->enrich($msg, $this->getMessage()->getChat()->getId());
        return $this;
    }

}