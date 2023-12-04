<?php

if (file_exists('config.php')) 
    $CONFIG = include('config.php');

if (!isset($CONFIG))  
    die('Arquivo de configurações não encontrado!');

$GLOBALS['_CONFIG'] = $CONFIG;

require_once('src/app/Route.php');
require_once('src/app/Db.php');

Db::init();
Route::run();