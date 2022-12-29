<?php
// Load composer
require_once __DIR__ . '/vendor/autoload.php';

use App\assets\consts;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Telegram;
use App\misc\local;
// Add you bot's API key and name
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$bot_api_key  = $_ENV['BOT_API_KEY'];
$bot_username = $_ENV['BOT_USERNAME'];


// Define all IDs of admin users in this array (leave as empty array if not used)

$admin_users = \App\assets\consts::ADMINS($_ENV);

// Define all paths for your custom commands in this array (leave as empty array if not used)
$commands_paths = [
//    __DIR__ . '/Commands/',
    __DIR__ . '/CustomCommands/',
];

// Create Telegram API object
$telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

// Add commands paths containing your custom commands
$telegram->addCommandsPaths($commands_paths);

// Enable admin users
$telegram->enableAdmins($admin_users);

// Enable MySQL
//$telegram->enableMySql($mysql_credentials);

$telegram->setUpdateFilter(function (Update $update, Telegram $telegram, &$reason = 'Update denied by update_filter') {
    if ($update->getMessage()->getCommand() !== 'start')
        return true;
    $id = $update->getMessage()->getFrom()->getId();
    $users = local::loadusers(consts::PARTICIPANTS());
    $users[$id] = strtolower($update->getMessage()->getFrom()->getUsername()
        ?? $update->getMessage()->getFrom()->getId());
    foreach (consts::ADMINS() as $admin)
        $users[$admin] = $users[$admin] ?? uniqid("admin_");

    local::storeusers($users, consts::PARTICIPANTS());
    return true;
});


$telegram->enableLimiter();

return $telegram;