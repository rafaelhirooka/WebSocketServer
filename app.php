<?php

require __DIR__ . '/bootstrap/app.php';

/**
 * On start script, clean database
 */
\Illuminate\Database\Capsule\Manager::table('active_connections')->delete();
\Illuminate\Database\Capsule\Manager::table('subscribes')->delete();

$app = new Ratchet\App($configW['host'], $configW['port'], '0.0.0.0');
$app->route('/', new \App\Sender($configS['secret']), array('*'));
$app->run();