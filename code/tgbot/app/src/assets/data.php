<?php

namespace App\assets;

use Dotenv\Dotenv;
use loandbeholdru\pipe\pipe;
use loandbeholdru\shorts\arrays;

class data
{
    protected static $defs;
    public static function ENV(string $name, array $env = null, $def = null)
    {
        $data = ($env ?? $_ENV ?? [])[$name] ?? $def;
        if ($data != $def) return $data;

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
        $dotenv->load();
        return $_ENV[$name] ?? $data;
    }

    public static function REG_HEADER(array $env = null)
    {
        return static::ENV('REG_HEADER', $env) ?? arrays::ifDefined(
            '', "REG_HEADER", 'xc-token');
    }
    public static function REG_TOKEN(array  $env = null)
    {
        return static::ENV('REG_TOKEN', $env) ?? arrays::ifDefined(
            '', "REG_TOKEN", static::defaults('token'));
    }
    public static function REG_URL(array  $env = null)
    {
        return static::ENV('REG_URL', $env) ?? arrays::ifDefined(
            '', "REG_URL", static::defaults('url'));
    }
    protected static function defaults(string $name){
        static::$defs = static::$defs
            ?? (require_once (__DIR__ . '/defaults.php'))(msg::EXCEPTION);
        return static::$defs[$name] ?? null;
    }

}