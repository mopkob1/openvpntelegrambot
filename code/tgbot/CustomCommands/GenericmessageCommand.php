<?php

/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use App\assets\consts;
use App\assets\msg;
use App\enrich\emptyMsg;
use Helper\defaultchatid;
use Helper\miscBroadcast;
use loandbeholdru\shorts\arrays;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

/**
 * Generic message command
 *
 * Gets executed when any type of message is sent.
 */
class GenericmessageCommand extends SystemCommand
{
    use defaultchatid;
    use emptyMsg;

    protected $name = 'genericmessage';
    protected $description = 'Handle generic message';
    protected $version = '1.1.0';
    protected $need_mysql = false;
    public function execute() : ServerResponse
    {
        try{
            $msg = file_get_contents(consts::EMPTY());
            consts::ENV("LOADIFNOTYET");
            $msg = arrays::substfields($msg, $_ENV);
        }catch (\Throwable $e){
            file_put_contents(ERRORLOG, $e->getMessage() . PHP_EOL, FILE_APPEND);
            $msg = null;
        }

        return empty($msg)
            ? Request::emptyResponse()
            : $this->enrich($msg, $this->chatid());
    }
}
