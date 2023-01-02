<?php

namespace App\assets;

use loandbeholdru\pipe\pipe;

class msg
{
    protected const undescribed = "Caption not described yet ...";
    const UNKNOWN = "'unknown'";
    const START_DESC = 'Start command';
    const SENDSMS_DESC = 'Command to send message';
    const EMPTY = "You can use commands from help (*/help*)";

    const EXCEPTION = "bashCommandErrorException.php";
    const HIRE_DESC = 'Command to hire employee';
    public static function hire(string $msg = null)
    {
        return static::ret([
            'WANT_TO' => 'Name: *%s %s*' . PHP_EOL
                . 'Username: *%s*' . PHP_EOL
                . 'User id: *%s*',
            'ALREADY' => 'You are already hired.',
            'ERROR' => "Can't hire user *%s*. Error: %s",
            'MSG' => "User *%s* just hired!",
            'REQUEST' => "I've send hiring request to bot admin." . PHP_EOL
                . "Please wait reaction.",
            'DONE' => "You are hired!"
        ], $msg);
    }
    const FIRE_DESC = 'Command to fire employee';
    public static function fire(string $msg = null)
    {
        return static::ret([

            'ERROR' => "Can't fire user: %s. Error: %s",
            'MSG' => "User *%s* just fired!",
            'DONE' => "You are fired!",
            'NOTFOUND' => "User not found!",
        ], $msg);
    }

    const HELP_DESC = 'Show bot commands help';
    public static function help(string $msg = null)
    {
        return static::ret([
            'COMMANDS_LIST' => '*Commands List*:' . PHP_EOL,
            'ADMINS_COMMANDS_LIST' => PHP_EOL . '*Admin Commands List*:' . PHP_EOL,
            'EXACT' => PHP_EOL . 'For exact command help type: /help <command>',
            'FORMAT' => 'Command: %s (v%s)' . PHP_EOL .
                'Description: %s' . PHP_EOL .
                'Usage: %s',
            'NOTFOUND' => 'No help available: Command /%s not found'
        ], $msg);
    }
    const STAT_DESC = 'Show usage statistics for today or yesterday. Only for admins.';
    public static function stat(string $msg = null)
    {
        return static::ret([
            'ERROR' => "You can't see statistic. Error: %s",
            'MSG' => "Statistic usage for %s: %s",
        ], $msg);
    }

    const SUBS_DESC = 'Command to subscribe employee or subscribe yourself';
    public static function subs(string $msg = null)
    {
        return static::ret([
            'ERROR' => "Can't subscribe user: %s. Error: %s",
            'MSG' => "User *%s* just subscribed!",
            'NOTFOUND' => "User not found in hired!",
            'DONE' => "You are subscribed!",
        ], $msg);
    }

    const UNSUBS_DESC = 'Command to unsubscribe employee or unsubscribe yourself';
    public static function unsubs(string $msg = null)
    {
        return static::ret([
            'MSG' => "User *%s* just unsubscribed!",
            'ERROR' => "Can't unsubscribe user: %s. Error: %s",
            'NOTFOUND' => "User not found!",
            'DONE' => "You are unsubscribed!",
        ], $msg);
    }

    public static function smsReader(string $msg = null)
    {
        return static::ret([
            'MSG' => "%s\n%s",
        ], $msg);


    }
    protected static function ret(array $dict, string $msg = null)
    {
        return $dict[$msg] ?? static::undescribed;
    }
    const NEWUSER_DESC = 'Command to create new Open VPN user';
    public static function newuser(string $msg = null)
    {
        return static::ret([
            'CREATED' => "Certs for user *%s* just created!",
            'ERROR' => "Can't prepare and download cert for user *%s*.",
            'PIPE_ERROR' => 'Some problems with pipe in newuser!',
            'CREATE_ERROR' => 'Problem with creating user!',
            'GET_ERROR' => 'Problem with getting files!',
            'NOTFOUND' => "You have to use '/start' command before '/%s'",
            'NOTREG' => "User *%s* not registered in the bot",
            'DONE' => "I just sent certificates to the user *%s*",
            'WANT_TO' => 'Somebody want to join with your VPN.' . PHP_EOL . PHP_EOL
                . 'Below personal him|her telegram info.' . PHP_EOL . PHP_EOL
                . 'Name: *%s %s*' . PHP_EOL
                . 'Username: *%s*' . PHP_EOL
                . 'User id: *%s*' . PHP_EOL . PHP_EOL
                . 'If you want to prepare certs for him|her, just copy command below.',
            'COM_TEMPLATE' => '*/newuser %s*',
            'ALREADY' => 'You certs are already prepared.',
            'NORIGHTS1' => "You are not admin. You can manage only you own cert with name *%s*.",
            'NORIGHTS2' => " I've change name from *%s* to *%s*",
            'REQUEST' => "I've send cert request to bot admin." . PHP_EOL
                . "Please wait reaction.",
        ], $msg);

    }

    const REVOKE_DESC = 'Command to revoke certs for existing Open VPN user';
    public static function revoke(string $msg = null)
    {
        return static::ret([
            'DONE' => "Certs for user *%s* just revoked!",
            'PIPE_ERROR' => 'Some problems with pipe in revoke!',
            'DEL_ERROR' => 'Problem with revoking user!',
            'NOTFOUND' => "User *%s* are not registered as VPN user.",
            'NORIGHTS' => "You can't revoke certs for other users (*%s*). Only own." ,
        ], $msg);
    }
    const DISABLE_DESC = 'Command to disable certs for existing Open VPN user';
    public static function disable(string $msg = null)
    {
        return static::ret([
            'DONE' => "User *%s* has disabled!",
            'PIPE_ERROR' => 'Some problems with pipe in disable!',
            'DIS_ERROR' => 'Problem with disabling user!',
            'NOTFOUND' => "User *%s* are not registered as VPN user.",
            'NORIGHTS' => "You can't disable *%s* user." ,
        ], $msg);
    }
    const ENABLE_DESC = 'Command to enable certs for existing Open VPN user';
    public static function enable(string $msg = null)
    {
        return static::ret([
            'DONE' => "User *%s* has enabled!",
            'PIPE_ERROR' => 'Some problems with pipe in enable!',
            'EN_ERROR' => 'Problem with enabling user!',
            'NOTFOUND' => "User *%s* are not registered as VPN user.",
            'NORIGHTS' => "You can't enable *%s* user." ,
        ], $msg);
    }
    const USERS_DESC = 'Show Open VPN users';
    public static function users(string $msg = null)
    {
        return static::ret([
            'PIPE_ERROR' => 'Some problems with pipe in users!',
            'ENABLED' => "*ENABLED*:\n\n%s",
            'DISABLED' => "*DISABLED*:\n\n%s",
            'USERS_ERROR' => 'Error when getting user list.',
            'EMPTY' => 'No users found.',

        ], $msg);
    }
    const CONNECT_DESC = 'Command to connect two Open VPN users';
    public static function connect(string $msg = null)
    {
        return static::ret([
            'DONE' => "User *%s*(%s) and user *%s*(%s) have connected!",
            'PIPE_ERROR' => 'Some problems with pipe in connect!',
            'ERROR' => 'Problem with connecting users!',
            'NOTFOUND' => "User *%s* are not registered as VPN user.",
            'NOPARAMS' => "You have to give two users for connecting!",
        ], $msg);
    }
    const PUBLIC_DESC = 'Command to public host in VPN';
    public static function public(string $msg = null)
    {
        return static::ret([
            'DONE' => "User *%s*(%s) has published!",
            'PIPE_ERROR' => 'Some problems with pipe in public!',
            'ERROR' => 'Problem with publishing user!',
            'NOTFOUND' => "User *%s* are not registered as VPN user.",
            'NOPARAMS' => "You have to give user for publishing!",
        ], $msg);
    }
    const HIDE_DESC = 'Command to hide host';
    public static function hide(string $msg = null)
    {
        return static::ret([
            'DONE' => "User *%s*(%s) has hided from all direct connections!",
            'PIPE_ERROR' => 'Some problems with pipe in hide!',
            'ERROR' => 'Problem with hiding user!',
            'NOTFOUND' => "User *%s* are not registered as VPN user.",
            'NOPARAMS' => "You have to give user for hiding!",
        ], $msg);
    }
    const OBSCURE_DESC = 'Command to obscure VPN Host of user1 (public host) from VPN Host of user2 (usually, individual host)';
    public static function obscure(string $msg = null)
    {
        return static::ret([
            'DONE' => "User *%s*(%s) has obscured from public host *%s*(%s)!",
            'PIPE_ERROR' => 'Some problems with pipe in obscure!',
            'ERROR' => 'Problem with obscuring user!',
            'PUB_ERROR' => 'You have to use username with public VPN Host as first parameter!',
            'NOTFOUND' => "User *%s* are not registered as VPN user.",
            'NOPARAMS' => "You have to give two registered vpn user for obscuring!",
        ], $msg);
    }
    const NET_DESC = 'Command to show visability map in VPN';
    public static function net(string $msg = null)
    {
        return static::ret([
            'PIPE_ERROR' => 'Some problems with pipe in users!',
            'ENABLED' => "*ENABLED*:\n\n%s",
            'DISABLED' => "*DISABLED*:\n\n%s",
            'USERS_ERROR' => static::users('USERS_ERROR'),
            'RULES_ERROR' => "Error when getting iptables-rules!",
            'FORPUBLIC' => "Public hosts:",
            'FORCONNECT' => "\n*connected* with hosts:",
            'FOROBSCURE' => "\n*obscured* from public hosts:",
            'NORULES' => "no rules, host connected only to public hosts.",
            'EMPTY' => "Iptables is empty."
        ], $msg);
    }
    const NEWVIRT_DESC = 'Add virtual username for Open VPN user';
    public static function newvirt(string $msg = null)
    {
        return static::ret([
            'DONE' => "New virtual user with name:\n\n   *%s* \nadded for account *%s*. \n\nAdd certs with command:",
        ], $msg);
    }
    const RMVIRT_DESC = 'Remove virtual username for Open VPN user';
    public static function rmvirt(string $msg = null)
    {
        return static::ret([
            'DONE' => "Virtual user with name:\n\n   *%s* \n\nhas removed from account *%s*.",
            'NOTFOUND' => "Virtual user with name *%s* not found in account *%s*.",
        ], $msg);
    }
    const VIRTS_DESC = 'Show all virtual usernames added for Open VPN user';
    public static function virts(string $msg = null)
    {
        return static::ret([
            'NOTFOUND' => "Virtual users for account *%s* - not found.",
            'LINE' => "*%s* (%s)",
            'DONE' => "List of virtual users for account *%s*:"
        ], $msg);
    }
    const ADMIN_DESC = 'Ask admins about something.';
    public static function admin(string $msg = null)
    {
        return static::ret([
            'EMPTY' => "Message hasn't to be empty.",
            'LINE' => "*%s*:",
            'REPLY' => "*/s %s*"
        ], $msg);
    }
    const S_DESC = 'Send message to user.';
    public static function s(string $msg = null)
    {
        return static::ret([
            'EMPTY' => "Message hasn't to be empty.",
            'NOTFOUND' => "User *%s* not found.",
            'LINE' => "*admin*:",

        ], $msg);
    }
    const STAFF_DESC = 'Reported about people.';
    public static function staff(string $msg = null)
    {

        return static::ret([
            'ADM_HEADER' => "Administrators:\n",
            'CAND_HEaDER' => "Not hired yet:\n",
            'USER_HEADER' => "Hired users:\n",
            'NO_USERS' => "\nNo one user yet.",
            'USER_SUBS' => "*%s* (subscribed)",
            'USER_UNSUBS' => "*%s* (unsubscribed)",
            'USER' => "*%s*",

        ], $msg);
    }
    const IMPART_DESC = 'Command to send one or more created certs to any user';
    public static function impart(string $msg = null)
    {
        return static::ret([
            'NOPARAMS' => 'You have to give one or two args.',
            'PIPE_ERROR' => 'Some problems with pipe in impart!',
            'GET_ERROR' => 'Problem with getting files!',
            'ERROR' => "Can't found certs for user *%s* and get follow errors: \n",
            'DONE' => "Certs for *%s* have sent.",
            'VIRT' => "User *%s* is virtual!",
            'NOTREG' => "User *%s* not found!",
            'NOCERTS' => "No user found with regexp: /.\*%s.\*/",
            'INFORM' => "Admin will send to you some files ..."
        ], $msg);
    }
}