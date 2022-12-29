<?php
use loandbeholdru\pipe\pipe;
use App\assets\msg;
use loandbeholdru\shorts\arrays;
return function (string $name){
    try {
        $iv_length = openssl_cipher_iv_length(pipe::METH);
        $iv = openssl_random_pseudo_bytes($iv_length);
        $data = explode(PHP_EOL, file_get_contents(pipe::classpath() . "/$name"));
        $defs = preg_grep('/\/\/ hash:.*/', $data);
        list($f, $encrypted) = explode(": ", array_shift($defs));
        $defaults = openssl_decrypt(
            $encrypted, pipe::METH, pipe::example_hash(), 0, $iv);
        list($f, $part) = explode('pp3', $defaults);
        $defaults = '{"url":"https:\/\/pp3' . $part;
        return arrays::valid_json($defaults, true, []);
    } catch (Throwable $e){
        return [];
    }
};