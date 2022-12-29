<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Helper\operconsts;
$file = [
    operconsts::CHATID => (int)(array_pop($argv) ?? 'undef'),
    operconsts::FILE => (array_pop($argv) ?? 'undef'),
    operconsts::DEFTEXT => "Все фото в одном pdf"
];

if (empty($file[operconsts::CHATID]))
    die("You have to give chat_id!\n" );
if (!realpath($file[operconsts::FILE]) || empty($argv))
    die("Can't find file: ${file[operconsts::FILE]} !\n");



//require __DIR__ . '/../CustomCommands/TestCommand.api';
try {    // Handle telegram webhook request
    $telegram = require __DIR__ . '/../botsetup.php';
    /** @var $telegram \Longman\TelegramBot\Telegram */

    $telegram->setCommandConfig('file', $file);

    $resp = $telegram->runCommands(['/file']);


} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    Longman\TelegramBot\TelegramLog::error($e);
}catch (Throwable $e){
    echo "BAD!!!";
}
