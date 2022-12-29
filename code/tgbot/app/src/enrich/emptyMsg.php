<?php

namespace App\enrich;

use Helper\miscBroadcast;

trait emptyMsg
{
    use miscBroadcast;
    protected function enrich($messages, int ...$ids)
    {
        return $this->broadcast($messages, ...$ids);
    }
}