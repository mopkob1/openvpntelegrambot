<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\assets\consts;
use App\assets\msg;
use loandbeholdru\pipe\bashCommandErrorException;
use loandbeholdru\pipe\brokenPipeException;
use loandbeholdru\pipe\pipecommand;
use loandbeholdru\pipe\piperesult;
use App\misc\local;
use App\share\miscGetters;
use Helper\miscBroadcast;
use Helper\miscCommands;
use Longman\TelegramBot\Commands\AdminCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use loandbeholdru\shorts\arrays;

class NetCommand extends AdminCommand
{
    use miscCommands;
    use miscBroadcast;
    use miscGetters;

    protected $name = 'net';                      // Your command's name
    protected $description = msg::NET_DESC; // Your command description
    protected $usage = '/net';                    // Usage of your command
    protected $version = '1.0.0';
    protected $need_mysql = false;

    public function execute(): ServerResponse
    {
        try {
            $info = [$this->chatid()];

            $this->chatAction($info);

            $msg = $this->net();

        }catch (\Throwable $e){
            $msg[] = $e->getMessage();
        }

        return $this->broadcast($msg ?? msg::net('EMPTY'), ...$info);
    }

    protected function net()
    {
        $this->askusers($users)
            ->askrules($rules);

        foreach ($users as $user){
            list($ip, $name, $id) = $this->extract($user);
            $connected = $this->lookfordirect($ip, $rules);
            $obscured = $this->lookforobscure($ip, $rules);
            $hosts["*$name* ($ip)"] = array_filter(compact('connected', 'obscured'));
            $net[$ip] = compact('name', 'id');
        }

        $public = $this->lookforpublic($rules);
        foreach ($public as $ip)
            $p = array_merge($p ?? [ msg::net("FORPUBLIC")], [
                sprintf("*%s* (%s)", $net[$ip]['name'], $ip)]);
        foreach ($hosts as $label => $host) {
            if (!empty($host['connected'])){
                $tmp[] = msg::net("FORCONNECT");
                $tmp[] = implode(
                    "\n", $this->formatter($net, $host['connected']));
            }
            if (!empty($host['obscured'])){
                $tmp[] = msg::net("FOROBSCURE");
                $tmp[] = implode(
                    "\n", $this->formatter($net, $host['obscured']));
            }
            $res[] = implode("\n", array_merge(
                ["$label:"], $tmp ?? [msg::net("NORULES")]
            ));
            $tmp = null;
        }

        return array_merge([implode("\n", $p ?? [])], $res);
    }

    protected function formatter($guide, $hosts)
    {
        foreach ($hosts as $host)
            $res[] = sprintf("*%s* (%s)", $guide[$host]['name'] ?? '', $host);
        return $res ?? [];
    }
    protected function extract(string $line)
    {
        $data = explode(":", $line);
        $user = arrays::first($data);
        $ip = arrays::first(explode(" ", $data[1]));
        list($id, $user) = $this->lookforname($user);

        return [$ip, $user, $id];
    }

    protected function lookfordirect(string $ip, $text)
    {
        $data = is_array($text) ? $text : explode("\n", $text);
        $data1 = preg_grep("/.+((-s|-d)\s+.+){2}.+\s+ACCEPT$/", $data);
        $data2 = preg_grep("/.+(\.0\/\d+)/", $data1, PREG_GREP_INVERT);
        $lines = preg_grep("/.+\s+-s\s+$ip.*/", $data2);
        foreach ($lines as $line)
            $hosts[] = preg_replace("/(.+\s+-d\s+)(.*)(\/.*)/","$2", $line);

        return $hosts ?? [];
    }

    protected function lookforobscure(string $ip, $text)
    {
        $data = is_array($text) ? $text : explode("\n", $text);
        $lines = preg_grep("/.+(-s\s+$ip).+\s+DROP$/", $data);
        foreach ($lines as $line)
            $hosts[] = preg_replace("/(.+\s+-d\s+)(.*)(\/.*)/","$2", $line);

        return $hosts ?? [];
    }

    protected function lookforpublic($text)
    {
        $data = is_array($text) ? $text : explode("\n", $text);
        $data1 = preg_grep("/.+((-s|-d)\s+.+){2}.+\s+ACCEPT$/", $data);
        $lines = preg_grep("/.+(\.0\/\d+)/", $data1);
        foreach ($lines as $line)
            $hosts[] = preg_replace("/(.+\s+-d\s+)(.*)(\/.*)/","$2", $line);

        return array_unique($hosts ?? []);
    }

    protected function askusers(&$users)
    {
        $result = new piperesult(
            consts::FILES(), new brokenPipeException(msg::net('PIPE_ERROR')));
        $command = new pipecommand(consts::PIPE(), 'users');

        $resp = local::releaseCommand(
            $command, $result, new bashCommandErrorException(msg::net('USERS_ERROR')));
        $users = array_filter(explode("\n", str_replace("\n\n", "\n", $resp)));

        return $this;
    }

    protected function askrules(&$rules = null)
    {
        $result = new piperesult(
            consts::FILES(), new brokenPipeException(msg::net('PIPE_ERROR')));
        $command = new pipecommand(consts::PIPE(), 'rules');

        $resp = local::releaseCommand(
            $command, $result, new bashCommandErrorException(msg::net('RULES_ERROR')));
        $rules = explode("\n", $resp);

        return $this;
    }
}