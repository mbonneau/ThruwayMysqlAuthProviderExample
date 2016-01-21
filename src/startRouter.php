<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/MysqlAuthProvider.php';

$loop = \React\EventLoop\Factory::create();

$router = new \Thruway\Peer\Router($loop);

// You need the authentication manager to do auth
$router->registerModule(new \Thruway\Authentication\AuthenticationManager());

// register our mysql auth provider for the "somerealm" realm
$mysqlAuth = new MysqlAuthProvider(['somerealm'], $loop);
$router->addInternalClient($mysqlAuth);

// add a transport so other people can connect
$router->registerModule(new \Thruway\Transport\RatchetTransportProvider("127.0.0.1", 9090));

$router->start();
