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
    '/get/dir/path/server'  => 'FileManager@getServerDirPath',
    '/get/dir/path/system'  => 'FileManager@getSystemDirPath',

    '/scan/dir/server'  => 'FileManager@scanServerDir',
    '/scan/dir/system'  => 'FileManager@scanSystemDir',
    '/scan/dir/child'   => 'FileManager@scanDirInit',
    '/file/content/get' => 'FileManager@loadFileContent',

    '/find/text'        => 'FindController@findInit',
    '/test/service/:param'  => 'FileManager@testAction',

//    '/find/file'        => 'FindController@findInit',
//    '/scan/dir'         => 'getOrder',
//    '/edit/file'        => 'rttt',
//    '/file/content/:arg' => 'rr',

];

$router = new Router($routes, $helper);

$response = [];

try {
    $response = $router->run();
} catch (\Exception $err) {
    $errorMessage = $err->getMessage();
    $response['error'] = $errorMessage;
}

die(json_encode($response, true));


//lg([ $response
//    //$router
//]);
//
//
////$url   = '/find/text@example.com. Is it correct?';
////$pattern = '|/find/text@([^\s\.]+\.[a-z]+)|';
//
//$url   = '/find/text';
//$pattern = '|/find/text|';
//$result = preg_match_all($pattern, $url, $matches);
//
//
//$url = "/find/text/retet/rty/456";
//$pattern = "|/find/text/[a-z]/*|";
//if (preg_match($pattern, $url))
//    echo "the url $url contains guru OK";
//else
//   echo "the url $url does not contain guru Not";
//
//lg([
//    $result,
//    $matches
//]);
//
//die;
//
//$pattern = '|^/find/text/[0-9]/[0-9]$|';
//$value = '/find/text/33/67';
//$r = preg_match($pattern, $value, $matches);
//
//lg([$r, $matches]);
//
//preg_match('/find/text/:type/:id', 'foobarbaz', $matches, PREG_OFFSET_CAPTURE);
//print_r($matches);
//
//
//return array(
//    '^/$' => 'controller=site&action=index',
//    '^/admin(?:/|)$' => 'module=admin&controller=site&action=index',
//    '^/admin/([a-z0-9]{1,15})(?:/|)$' => 'module=admin&controller=$1&action=index',
//    '^/admin/([a-z0-9]{1,15})/([0-9]{1,15})(?:/|)$' => 'module=admin&controller=$1&action=view&id=$2',
//
//);
//
//$router->run();
//
//$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
//$segments = explode('/', trim($uri, '/'));
//
//
//lg([
//    trim($_SERVER['PATH_INFO'], '/'),
//    $router
//]);

