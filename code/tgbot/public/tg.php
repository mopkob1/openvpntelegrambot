<?php

try {    // Handle telegram webhook request
    /** @var $telegram \Longman\TelegramBot\Telegram */
    $telegram = require __DIR__ . '/../botsetup.php';
    $telegram->handle();

} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    file_put_contents(ERRORLOG, $e->getMessage() . PHP_EOL, FILE_APPEND);
    Longman\TelegramBot\TelegramLog::error($e);
}catch (Throwable $e){
    file_put_contents(ERRORLOG, $e->getMessage() . PHP_EOL, FILE_APPEND);
}
