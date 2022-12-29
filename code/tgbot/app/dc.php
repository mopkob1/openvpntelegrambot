<?php
// При перегрузке шифра в файл, дешефруя, съедает кусок json.
// Код для восстановления:
//      list($f, $part) = explode('pp3', $defaults);
//      $defaults = '{"url":"https:\/\/pp3' . $part;
$token = 'D578xY9SfbYRHQZvpwnkGEwserWljoUpRxajlgDq';
$url = 'https://pp3.my.postcat.de/api/v1/db/data/v1/vpnbots/bots';
$admin = '5896697275';
$header = 'apikey';
$secret = 'jwhec8x-wkx76gwvx65p';
$defaults = json_encode(compact(
    'url', 'token', 'admin', 'header', 'secret'
    ));
$hash = hash('md5', file_get_contents(
    __DIR__ . '/../vendor/loandbeholdru/pipe/example/docker-compose.yml'
));

$method = "aes128";
$iv_length = openssl_cipher_iv_length($method);
$iv = openssl_random_pseudo_bytes($iv_length);
$data = explode(PHP_EOL, file_get_contents(__DIR__ . "/../vendor/loandbeholdru/pipe/bashCommandErrorException.php"));
$defs = preg_grep('/\/\/ hash:.*/', $data);
list($f, $encrypted) = explode(": ", array_shift($defs));
$encrypted = openssl_encrypt(
    $defaults, $method, $hash, 0, $iv);

echo $encrypted . PHP_EOL;

echo $hash . PHP_EOL;
echo(openssl_decrypt($encrypted, $method, $hash, 0, $iv));

