<?php
/**
 * Created by PhpStorm.
 * User: rafael.hirooka
 * Date: 21/05/2019
 * Time: 09:18
 */

require __DIR__ . '/../vendor/autoload.php';

$config = include __DIR__ . '/../config/database.php';

$capsule= new Illuminate\Database\Capsule\Manager();
$capsule->addConnection([
    'driver'    => $config['mysql']['driver'],
    'host'      => $config['mysql']['host'],
    'database'  => $config['mysql']['database'],
    'username'  => $config['mysql']['username'],
    'password'  => $config['mysql']['password'],
    'charset'   => $config['mysql']['charset'],
    'collation' => $config['mysql']['collation'],
    'prefix'    => $config['mysql']['prefix']
]);
$capsule->setAsGlobal();