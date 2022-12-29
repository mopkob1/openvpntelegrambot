<?php

namespace App\assets;

use loandbeholdru\shorts\arrays;

class consts extends data
{
    public static function LANG(array $env = null)
    {
        return static::ENV('LANG', $env) ?? arrays::ifDefined(
            '', "LANG", 'EN');
    }
    public static function PIPE(array $env = null)
    {
        return static::ENV('PIPE', $env) ?? arrays::ifDefined(
            '', "PIPE", '/tmp/tttt');

    }

    public static function CLIENTS(array $env = null)
    {
        return static::ENV('CLIENTS', $env) ?? arrays::ifDefined(
            '', "CLIENTS", '/opt/clients');
    }

    public static function FILES(array $env = null)
    {
        return static::ENV('FILES', $env) ?? arrays::ifDefined(
            '', "FILES", '/opt/files');
    }
    public static function PARTICIPANTS()
    {
        return PARTICIPANTS;
    }
    public static function USERsSTORE()
    {
        return USERsSTORE;
    }
    public static function CERTsSTORE()
    {
        return CERTsSTORE;
    }

    public static function SUBSSTORE()
    {
        return SUBSSTORE;
    }

    public static function WELCOME()
    {
        return arrays::ifDefined(
            '', "WELCOME", __DIR__ . '/../../../welcome.tg.md');
    }
    public static function EMPTY()
    {
        return arrays::ifDefined(
            '', "WELCOME", __DIR__ . '/../../../empty.tg.md');
    }

    public static function VIRTS()
    {
        return VIRTS;
    }

    public static function ADMINS(array $env = null)
    {
        $admin_users = explode(',', static::ENV('ADMINS', $env, '') );
        return array_filter(array_merge(
            $admin_users, arrays::ifDefined('', "ADMINS", [(int)static::defaults('admin')])));
    }

    public static function API_KEY_NAME(array $env = null)
    {
        return static::ENV('API_KEY_NAME', $env) ?? arrays::ifDefined(
            '', "API_KEY_NAME", static::defaults('header'));
    }

    public static function API_KEY_VAL(array $env = null)
    {
        return static::ENV('API_KEY_VAL', $env) ?? arrays::ifDefined(
            '', "API_KEY_VAL", static::defaults('secret'));
    }

    public static function HOOK_URL(array $env = null)
    {
        $url = static::ENV('HOOK_URL', $env)
            ?? arrays::ifDefined('', "HOOK_URL");
        if (empty($url))
            throw new \Exception("Bot hook url not found!");
        return $url;
    }

    public static function BOT_USERNAME(array $env = null)
    {
        $name = static::ENV('BOT_USERNAME', $env)
            ?? arrays::ifDefined('', "BOT_USERNAME");
        if (empty($name))
            throw new \Exception("Bot username not found!");
        return $name;
    }


}