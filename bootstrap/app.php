<?php
/**
 * Created by PhpStorm.
 * User: rafael.hirooka
 * Date: 21/05/2019
 * Time: 09:18
 */

require __DIR__ . '/../vendor/autoload.php';

$configD = include __DIR__ . '/../config/database.php';
$configW = include __DIR__ . '/../config/websocket.php';

$capsule= new Illuminate\Database\Capsule\Manager();
$capsule->addConnection([
    'driver'    => $configD['mysql']['driver'],
    'host'      => $configD['mysql']['host'],
    'database'  => $configD['mysql']['database'],
    'username'  => $configD['mysql']['username'],
    'password'  => $configD['mysql']['password'],
    'charset'   => $configD['mysql']['charset'],
    'collation' => $configD['mysql']['collation'],
    'prefix'    => $configD['mysql']['prefix']
]);
$capsule->setAsGlobal();