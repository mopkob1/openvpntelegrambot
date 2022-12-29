<?php

namespace App\enrich;

use Helper\miscBroadcast;

trait startMsg
{
    use miscBroadcast;
    protected function enrich($messages, int ...$ids)
    {
        return $this->broadcast($messages, ...$ids);
    }
}