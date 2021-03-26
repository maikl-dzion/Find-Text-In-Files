<?php

set_time_limit(0);

header('Content-Type: text/html; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

define('CURRENT_DIR'  , __DIR__);
define('SERVER_DIR'   , $_SERVER['DOCUMENT_ROOT']);

$fileSystem     = new FileSystemManagement();
$findController = new FindTextToFiles();
$router = new Router($fileSystem, $findController);
$results = $router->run();

$response = new Response();
$response->json($results);


class Response {

    public function json($results) {
        die(json_encode($results));
    }

}

////////////////////////////
/// ROUTER CLASS
///
class Router {

    protected  $request;
    protected  $server;
    protected  $action;
    protected  $fileSystem;
    protected  $findObject;

    public function __construct($fileSystem, $findObject) {
        $this->request = $_REQUEST;
        $this->server  = $_SERVER;
        $this->fileSystem  = $fileSystem;
        $this->findObject  = $findObject;
        $this->action  = $this->getRequest('action');
    }

    protected function getRequest($fieldName) {
        $value = false;
        if(!empty($this->request[$fieldName]))
            $value = $this->request[$fieldName];
        return $value;
    }

    public function run() {

        $result = array();
        $action = $this->action;
        $path   = $this->getRequest('path');
        $searchValue   = $this->getRequest('value');
        $type   = $this->getRequest('type');

        switch ($action) {

            case 'GET_SYSTEM_PATH':
                $result = $this->fileSystem->getRootPath('system');
                break;

            case 'GET_APACHE_PATH':
                $result = $this->fileSystem->getRootPath('apache');
                break;

            case 'SCAN_ROOT_DIR':
                $result = $this->fileSystem->getRootDirList($type);
                break;

            case 'GET_CHILD_DIR':
                $result = $this->fileSystem->scanDir($path);
                break;

            case 'GET_FILE_CONTENT':
                $result = $this->fileSystem->getFileContent($path, $type);
                break;

            case 'FIND_TEXT':

                $treeDirItems = $this->findObject->find($path, $searchValue);
                $resultInfo   = $this->findObject->getResults();

                //$treeDirItems   = $find->getTreeItems();
                //$indexFiles     = $find->getIndexFiles();

                if (empty($resultInfo)) {
                    $messageInfo = "Совпадений в файлах не найдено, нет результатов ";
                    $findResult = array('error' => $messageInfo);
                    die(json_encode($findResult));
                }

                $result = array(
                    'result'     => $resultInfo,
                    //'treeItems'  => $treeDirItems,
                    //'indexFiles' => $indexFiles,
                );

                break;
        }

        return $result;
    }
}


interface FileSystemInterface {
    public function scanDir($path);
    public function getRootDirList();
    public function getFileContent($path, $type);
}


////////////////////////////////
/// УПРАВЛЕНИЕ ДИРЕКТОРИЯМИ И ФАЙЛАМИ
///

class FileSystemManagement  {

    protected $files;
    protected $systemName;
    protected $systemDir;
    protected $serverDir;
    protected $errors = array();

    public function __construct() {
        $this->setSystemName();
        $this->setRootDirs();
    }

    public function getFiles() {
        return $this->files;
    }

    public function fileLoading($filePath){
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($filePath).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
    }

    public function getFileContent($filePath, $type = 'file') {

        if(!file_exists($filePath)) {
            $this->errors[] = 'Такой файл "' . $filePath . '" не существует';
            return false;
        }

        $result = array();

        switch ($type) {
            case 'file' :
                $result = file($filePath);
                break;

            case 'text' :
                $result = file_get_contents($filePath);
                break;

            case 'load' :
                $this->fileLoading($filePath);
                break;
        }

        return $result;
    }

    protected function setRootDirs() {
        $serverDir = $_SERVER['DOCUMENT_ROOT'];
        $this->serverDir = $serverDir;
        $systemArr = explode('/', $serverDir);
        $this->systemDir = (!empty($systemArr[0])) ? $systemArr[0] : '/';
    }

    protected function setSystemName() {
        $osName  = php_uname();
        $subject = 'Windows';
        if(stristr($osName, $subject) === false)
            $osName = 'linux';
        else
            $osName = 'windows';
        $this->systemName = $osName;
    }

    public function getRootDirList($type = 'apache') {
        $this->files  = array();
        if($type == 'apache') $dirPath = $this->serverDir;
        else                  $dirPath = $this->systemDir;
        return $this->scanDir($dirPath);
    }

    public function getRootPath($type = 'apache') {
        if($type == 'apache') $dirPath = $this->serverDir;
        else                  $dirPath = $this->systemDir;
        return array('path' => trim($dirPath));
    }

    public function scanDir($path, $recursive = false){

        if(!file_exists($path)){
            $this->errors[] = 'Такой директории "' .$path. '" не существует';
            return false;
        } elseif(!is_dir($path)) {
            $this->errors[] = '"' .$path. '" не является директорией';
            return false;
        }

        $files = glob($path ."/*");
        // $files = scandir($path . DIRECTORY_SEPARATOR);
        // lg($files);

        $files = array_diff($files, array('.', '..'));

        if(count($files) == 0) return $this->files;

        $funcName = __FUNCTION__;

        foreach($files as $file){

            $name = basename($file);
            $realPath = $path . DIRECTORY_SEPARATOR . $name;
            $fileInfo = array(
                'name' => $name,
                'path' => $realPath
            );

            if(is_file($realPath)){
                $fileInfo['type'] = 'file';
                $fileInfo['size'] = filesize($file);
                $this->files[$name] = $fileInfo;
            } elseif(is_dir($realPath)){
                $fileInfo['type'] = 'dir';
                $this->files[$name] = $fileInfo;
                if($recursive) {
                    $this->files[$name]['child'] = $this->$funcName($realPath);
                }
            }
        }

        return $this->files;
    }
}


/////////////////////////////////////
/// КЛАСС для ПОИСКА ТЕКСТА В ФАЙЛАХ
///

class FindTextToFiles
{
    public $resultsInfo  = array();
    public $treeDirItems = array();
    public $indexFiles  = array();
    public $searchValue = '';
    public $searchDir   = '';
    public $errorExit   = '';

    public function __construct($searchDir = '', $searchValue = '')
    {
        $this->searchValue  = $searchValue;
        $this->searchDir    = $searchDir;
        // $this->treeDirItems = $this->find($dir);
    }

    public function find($searchDir = '', $searchValue = '')
    {
        $treeDirItems = array();

        if($searchDir)
            $this->searchDir = $searchDir;

        if($searchValue)
            $this->searchValue = $searchValue;

        if (!is_dir($searchDir))
            lg('Такая директория отсутствует -- ' . $searchDir);

        if(!$searchValue)
            return false;

        // $files = glob($searchDir ."/*");
        $files = scandir($searchDir);

        // lg($files);

        foreach ($files as $key => $value) {

            if ($this->errorExit) {
                $this->getError();
                return $treeDirItems;
            }

            if ($value == '.' || $value == '..') continue;

            $sourceName = $searchDir . DIRECTORY_SEPARATOR . $value;
            $isFile   = is_file($sourceName);
            $state    = stat($sourceName);
            $realPath = realpath($sourceName);
            $pathInfo = pathinfo($sourceName);

            $treeItem = array();
            $treeItem['name'] = $value;
            $treeItem['path'] = $sourceName;
            $treeItem['child'] = array();

            if ($isFile) {
                $this->findWordToFile($sourceName);
                $treeItem['type'] = 'file';
            } else {
                $funcName = __FUNCTION__;
                $child = $this->$funcName($sourceName);
                $treeItem['type'] = 'folder';
                $treeItem['child'] = $child;
            }

            $treeDirItems[] = $treeItem;
        }

        $this->treeDirItems = $treeDirItems;

        return $treeDirItems;
    }

    protected function findStringContains($string, $searchValue) {
        $result = false;
        if (str_contains($string, $searchValue))
            $result = true;
        return $result;
    }

    public function findFopen($filePath, $searchValue) {
        $matches = array();
        $handle = fopen($filePath, "r");
        if ($handle) {
            while (!feof($handle)) {
                $buffer = fgets($handle);
                if(strpos($buffer, $searchValue) !== false)
                    $matches[] = $buffer;
            }
            fclose($handle);
        }
        return $matches;
    }

    public function findWordToFile($fileName) {

        $_count = 0;
        $contentList = file($fileName);
        $this->indexFiles[] = $fileName;

        $searchValue = $this->searchValue;
        $results = array();

        foreach ($contentList as $num => $value) {

            if (!$value) continue;

            if (strpos($value, $searchValue, 0) !== false) {
                $_count++;
                $results[$num] = array(
                    'string' => $searchValue,
                    'path'   => $fileName,
                    'num'    => $num,
                    'line'   => $value,
                );
            }
        }

        if ($_count)
            $this->resultsInfo[$fileName] = $results;
    }

    public function getResults() {
        return $this->resultsInfo;
    }

    public function getTreeItems() {
        return $this->treeDirItems;
    }

    public function getIndexFiles() {
        return $this->indexFiles;
    }

    public function getError($error = '') {
        print_r($this->errorExit);
        print_r($error);
        die;
    }
}




function lg() {

    $out = '';
    $get = false;
    $style = 'margin:10px; padding:10px; border:3px red solid;';
    $args = func_get_args();
    foreach ($args as $key => $value) {
        $itemArr = array();
        $itemStr = '';
        is_array($value) ? $itemArr = $value : $itemStr = $value;
        if ($itemStr == 'get') {
            $get = true;
        }

        $line = print_r($value, true);
        $out .= '<div style="' . $style . '" ><pre>' . $line . '</pre></div>';
    }

    $debugTrace = debug_backtrace();
    $line = print_r($debugTrace, true);
    $out .= '<div style="' . $style . '" ><pre>' . $line . '</pre></div>';

    if ($get) {
        return $out;
    }

    print $out;
    exit;
}