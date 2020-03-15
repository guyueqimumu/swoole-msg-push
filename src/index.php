<?php
include "../vendor/autoload.php";

const CONFIG_PATH = __DIR__ . '/config/config.php';

const API_PATH ="msgPush\\Api\\Controller";

$config = include CONFIG_PATH;

$protocol = $config['protocol'] ?? 'Http';

$class = "msgPush\\Protocol\\" . $protocol;

$obj = new $class($config);
$obj->start();

