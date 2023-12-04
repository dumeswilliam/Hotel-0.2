<?php
require_once('./src/request/CheckRequest.php');
require_once('./src/request/PersonRequest.php');

abstract class Route {

    const ROUTES = [
        'person' => 'PersonRequest::tolist',
        'person/read' => 'PersonRequest::toread',
        'person/create' => 'PersonRequest::tocreate',
        'person/update' => 'PersonRequest::toupdate',
        'person/delete' => 'PersonRequest::todelete',

        'check' => 'CheckRequest::tolist',
        'check/read' => 'CheckRequest::toread',
        'check/create' => 'CheckRequest::tocreate',
        'check/update' => 'CheckRequest::toupdate',
        'check/delete' => 'CheckRequest::todelete',
    ];

    public static function run() {
        $path = implode('/', array_filter(explode('/', $_SERVER['PATH_INFO'])));
        if (array_key_exists($path, self::ROUTES)) {
            print_r(self::ROUTES[$path]($_REQUEST));
        }
    }

}