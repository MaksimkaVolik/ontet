<?php
session_start();

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/Config.php';
require __DIR__ . '/Functions.php';

use Core\Router;
use Core\Database;

$db = new Database(
    'localhost', 
    'mvolikfg_2', 
    'Mvolik683', 
    'mvolikfg_forum'
);

function autoload($className) {
    $file = __DIR__ . "/../" . str_replace('\\', '/', $className) . ".php";
    if (file_exists($file)) {
        require $file;
    }
}

spl_autoload_register('autoload');