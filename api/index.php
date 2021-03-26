<?php

set_time_limit(0);

header('Content-Type: text/html; charset=utf-8');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');

// header('Access-Control-Allow-Credentials', true);
// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
// header('Access-Control-Allow-Headers: X-Requested-With, X-HTTP-Method-Override, Origin, Content-Type, Cookie, Accept');

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

date_default_timezone_set('Europe/Moscow');

include_once 'FindServiceController.php';

$manager = new FileManager();
$finder  = new FindController();
$helper  = new Helper();

$routes = [

    '/get/dir/path/:path_type'  => 'FileManager@getPathToSystem',
    '/scan/dir/:scan_type'      => 'FileManager@selectScanDir',
    '/scan/dir/child'           => 'FileManager@scanDirInit'  ,
    '/file/content/get'         => 'FileManager@loadFileContent',

    '/find/text'                => 'FindController@findInit',
    '/test/service/:param'      => 'FileManager@testAction',

    //    '/get/dir/path/server'  => 'FileManager@getServerDirPath',
    //    '/get/dir/path/system'  => 'FileManager@getSystemDirPath',
    //    '/scan/dir/server'      => 'FileManager@scanServerDir',
    //    '/scan/dir/system'      => 'FileManager@scanSystemDir',
];

$router = new Router($routes, $helper);

try {
    $response = $router->run();
} catch (\Exception $e) {
    $errorMessage = $e->getMessage();
    $response['error'] = $errorMessage;
}

// lg($response);

die(json_encode($response, true));


