<?php

namespace App\misc;

use App\assets\consts;

use loandbeholdru\pipe\pipecommand;
use loandbeholdru\pipe\piperesult;

use loandbeholdru\slimcontrol\api\otherException;
use loandbeholdru\shorts\arrays;

class local
{
    public static function storeusers(array $users, string $storepath)
    {
        $json = json_encode($users);
        if (!file_put_contents($storepath, $json))
            throw new otherException("Can't write file: $storepath");
    }

    public static function loadusers(string $storepath)
    {
        return arrays::valid_json(
            file_get_contents($storepath), true, []);
    }

    public static function checkInList(int $admin, \Exception $exception, int ...$admins)
    {
        if (!in_array($admin, $admins))
            throw $exception;
    }

    public static function checkeusers($useridORname, string $store = null, \Exception $e = null)
    {
        $store = $store ?? consts::USERsSTORE();
        $users = static::loadusers($store);
        $result = arrays::isAssoc($users) ?
            !empty($users[$useridORname]) : in_array($useridORname, $users);

        if (empty($e)) return $result;

        if (!$result) throw $e;

        return $result;
    }

    public static function releaseCommand(
        pipecommand $command, piperesult $result, \Exception $oncommand = null, int $time = 1)
    {
        fwrite($command->pipe($result->onpipe()), "$command");
        sleep($time);
        return $result->read($command->unic(), $oncommand);
    }

    public static function adduser($nameORarray, string $storepath)
    {
        $users = static::loadusers($storepath);
        $users = arrays::isAssoc($nameORarray)
            ? $nameORarray + $users
            : array_merge($users, is_array($nameORarray) ? $nameORarray : [$nameORarray]);
        static::storeusers($users, $storepath);
        return $users;
    }

    public static function deluser($nameORarray, string $storepath)
    {
        $users = static::loadusers($storepath);
        $users = arrays::isAssoc($nameORarray)
            ? array_diff_assoc($users, $nameORarray)
            : array_diff($users , is_array($nameORarray) ? $nameORarray : [$nameORarray]);
        static::storeusers($users, $storepath);
        return $users;
    }
}