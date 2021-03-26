<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Content-Type: text/html; charset=utf-8');

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

//define('ERROR', 'error');
//define('DEBUG', 'debug');
//define('CLASSES_DIR', __DIR__ . '/classes');

define('ROOT_DIR', __DIR__);

$DBConfig = array(
    'host'   => 'bolderp5.beget.tech',
    'dbname' => 'bolderp5_laravel',
    'user'   => 'bolderp5_laravel',
    'passwd' => '1985list',
    'driver' => 'mysql',
    'port'   => 3306,
    // 'driver' => 'pgsql',
    // 'port'   => 5432,
    // 'dsn'    => 'postgres:host=192.168.3.23;dbname=ibis;charset=utf8',
);


include_once __DIR__ . '/classAutoloader.php';
spl_autoload_register('Autoloader::autoload');

// $db     = new DB($DBConfig);

// ##############################################
// ОБРАБОТКА ВХОДЯЩИХ ЗАПРОСОВ  #################
try {

    $route = new Router($DBConfig); // Запускаем роутер
    $result = $route->run();        // Запускаем контроллер
    getResponse($result);           // Возвращаем результат

}
catch (ErrorException $e)  {
    getError($e);
}

// КОНЕЦ ОБРАБОТКА    ###########################
// ##############################################


class Router {

    public $pathInfo = '';
    public $requestUri;
    public $queryParam;
    public $phpSelf;
    public $server;
    public $method;
    public $params = array();
    public $dbconfig;
    public $route = array('controller' => 'DefaultController',
                          'action'     => 'index');

    public function __construct($dbconfig) {
        $this->dbconfig = $dbconfig;
        $this->setRouteParams();
        $this->pathInfoInit();  // берем из PATH_INFO
        // $this->queryParamInit(); // берем из QUERY_STRING
    }

    public function run() {

        $Controller = $this->route['controller'];
        $Action     = $this->route['action'];

        $errMessage = 'Не найден класс';
        if(class_exists($Controller)) {
            $curController = new $Controller($this->dbconfig, $this->params);
            if(method_exists($curController, $Action)) {
                return $curController->$Action();
            }
            $errMessage = 'Не найден метод в класс';
        }

        getError(array('message' => $errMessage,
                       'route'   => $this->route));
    }

    protected function setRouteParams() {
        $this->server    = $_SERVER;

        if(!empty($_SERVER['PATH_INFO']))
            $this->pathInfo = $_SERVER['PATH_INFO'];

        $this->requestUri  = $_SERVER['REQUEST_URI'];
        $this->queryParam  = $_SERVER['QUERY_STRING'];
        $this->method      = $_SERVER['REQUEST_METHOD'];
        $this->phpSelf     = $_SERVER['PHP_SELF'];
    }

    protected function pathInfoInit() {
        $route = explode('/', ltrim($this->pathInfo, '/'));

        foreach ($route as $key => $value) {
            $value = trim($value);
            if(!$value) continue;
            switch ($key) {
                case 0  : $this->route['controller'] = $value; break;
                case 1  : $this->route['action']     = $value; break;
                default : $this->params[] = $value; break;
            }
        }
    }

    protected function queryParamInit() {
        $route = explode('&', $this->queryParam);
        foreach ($route as $key => $value) {
            $value = explode('=', $value);
            switch ($key) {
                case 0  : $this->route['controller'] = trim($value[1]); break;
                case 1  : $this->route['action']     = trim($value[1]); break;
            }
        }
    }

    protected function getParam($name) {
         $result = false;
         if(!empty($_GET[$name])) {
             $result = $_GET[$name];
         }
         return $result;
    }

    protected function requestUriInit() {
        $route = explode('/', ltrim($this->requestUri, '&'));
    }

}


////////////////////////////////////////////
//     FUNCTIONS HELPER  //////////////////

function getError($data) {
    getResponse($data, 'error');
}

function getDebug($data) {
    getResponse($data, 'debug');
}

function getResponse($data, $name = 'data') {
    die(json_encode(array($name => $data)));
}

function lg($data, $level = 0) {
    // $trace = debug_backtrace();
    echo "\n\n ||||||||| === DATA  === ||||||||| \n\n";
    echo print_r($data, true);

    if($level == -1) die;

    echo "\n\n ||||||||| === TRACE === ||||||||| \n\n";
    $arrTrace = debug_backtrace ();
    $resTrace = array();

    foreach ($arrTrace as $key => $values) {
        if($level > 3) break;
        $item = array();
        foreach ($values as $fname => $value) {
            switch($fname) {
                case  'file' :
                case  'line' :
                case  'function' :
                    $item[$fname] = $value;
                    break;
            }
        }
        $level++;
        if(!empty($item))
            $resTrace[$key] = $item;
    }

    echo print_r($resTrace, true);
    die;
}

function isEmpty($arr = array(), $fieldName = '') {
    $result = '';
    if(!empty($arr[$fieldName])) {
        $result = $arr[$fieldName];
    }
    return $result;
}


//function lg($data) {
//    echo "<pre>" .print_r($data, true) . "</pre>";
//    die;
//}