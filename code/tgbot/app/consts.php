<?php


define('STORE', __DIR__ . '/../Store');
define('SUBSSTORE', STORE . '/subs.json');
define('USERsSTORE', STORE . '/users.json');
define('CERTsSTORE', STORE . '/certs.json');
define('PARTICIPANTS', STORE . '/participants.json');
define('STATSTORE', STORE . '/stat');
define('STATSTOREFILE', STATSTORE . '/stat_%s.json');
define('VIRTS', STORE . '/virtuals.json');
define('PIPE', '/opt/pipes/vpnpipe');

//------------------ You have to change bellow
date_default_timezone_set('Europe/Moscow');

//------------------

define('PATHS', [ STORE, STATSTORE]);


foreach (PATHS as $PATH) if (!realpath($PATH))
    mkdir($PATH, 0777, true);