<?php

namespace App\enrich;

use Helper\miscBroadcast;
use Longman\TelegramBot\Request;

trait emptyMsg
{
    use miscBroadcast;
    protected function enrich($messages, int ...$ids)
    {
        try {
            return $this->broadcast($messages, ...$ids);
        }catch (\Throwable $e){
            file_put_contents(
                ERRORLOG,  $e->getMessage() . PHP_EOL, FILE_APPEND);

        }
        return Request::emptyResponse();
    }
}