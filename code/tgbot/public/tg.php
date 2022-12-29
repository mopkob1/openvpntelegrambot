<?php

try {    // Handle telegram webhook request
    /** @var $telegram \Longman\TelegramBot\Telegram */
    $telegram = require __DIR__ . '/../botsetup.php';
    $telegram->handle();

} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    Longman\TelegramBot\TelegramLog::error($e);
}catch (Throwable $e){
    echo "BAD!!!";
}
