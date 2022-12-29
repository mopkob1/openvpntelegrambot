<?php

namespace App\other;

use App\assets\consts;
use App\assets\msg;

use loandbeholdru\shorts\arrays;


class smsReader extends telegramResend
{


    protected function buildMessage(string &$text = null)
    {
        $text = sprintf(msg::smsReader('MSG'),
            $this->args['phone'] ?? "", $this->args['body'] ?? "");
        return $this;
    }

    protected function users()
    {
        return arrays::valid_json(
            file_get_contents(consts::SUBSSTORE()), true, [consts::ADMINS()]);
    }



    protected function ins()
    {
        return ['body', 'phone', consts::API_KEY_NAME(), 'date', 'type'];
    }

    protected function asss()
    {
        return ['body', 'phone', consts::API_KEY_NAME()];
    }


}