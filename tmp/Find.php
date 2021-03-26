<?php

set_time_limit(0);

header('Content-Type: text/html; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

define('CURRENT_DIR'   , __DIR__);
define('SERVER_DIR'    , $_SERVER['DOCUMENT_ROOT']);
define('INDEXED_FILES_FNAME' , 'indexed_files.ini');
define('INDEXED_DIRECTORIES_FNAME' , 'indexed_directories.ini');

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
    protected  $find;

    public function __construct($fileSystem, $findObject) {
        $this->request = $_REQUEST;
        $this->server  = $_SERVER;
        $this->fileSystem  = $fileSystem;
        $this->find  = $findObject;
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

            case 'INDEX_DIRECTORY':
                $result = $this->find->indexingDirInit($path);
                break;

            case 'FIND_TEXT':

                $startTime = date('Y-m-d__H-i-s');
                $startMem = memory_get_usage();

                $this->find->scan($path, $searchValue);
                $results   = $this->find->getResults();

                $endMem = memory_get_usage();
                $endTime = date('Y-m-d__H-i-s');

                $errorInfo = '';

                $result = array(
                    'error'  => $errorInfo,
                    'memory' => array($startMem, $endMem),
                    'time'   => array($startTime, $endTime),
                    'result' => $results,
                );

                // lg($result);

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
    public $results = array();
    public $errors  = array();
    protected $indexedFiles = array();
    protected $indexedDirectories = array();

    public $searchValue = '';
    public $searchDir   = '';

    public function __construct($searchDir = '', $searchValue = '')
    {
        $this->searchValue  = $searchValue;
        $this->searchDir    = $searchDir;
    }

    public function indexingDirInit($dirName = '') {

        $level = 0;
        $this->indexedFiles = array();
        $this->indexedDirectories = array();

        $this->directoryIndexing($dirName, $level);

        $filePath = $dirName . DIRECTORY_SEPARATOR . INDEXED_FILES_FNAME;
        $fstate = $this->saveIndexingToFile($filePath, $this->indexedFiles);

        $filePath = $dirName . DIRECTORY_SEPARATOR . INDEXED_DIRECTORIES_FNAME;
        $dstate = $this->saveIndexingToFile($filePath, $this->indexedDirectories);

        return array(
            'files' => $fstate,
            'dirs'  => $dstate
        );
    }

    protected function saveIndexingToFile($filePath, $data) {
        if(empty(($data)))
            return false;

        if(file_exists($filePath))
            unlink($filePath);

        $resultString = '';
        foreach ($data as $key => $values) {
            $resultString .= "{$key}\n";
        }

        if($resultString)
            return file_put_contents($filePath, $resultString);
        return false;
    }

    protected function directoryIndexing($dirName = '', $level = 0) {
        if((!$dirName) || (!file_exists($dirName)) || (!is_dir($dirName))  )
            $dirName = $_SERVER['DOCUMENT_ROOT'];

        $level++;
        $files = glob($dirName ."/*");
        $files = array_diff($files, array('.', '..'));

        foreach ($files as $key => $filePath) {
            if (is_file($filePath)) {
                $this->indexedFiles[$filePath] = $filePath;
            } else {
                $this->indexedDirectories[$filePath] = $filePath;
                $funcName = __FUNCTION__;
                $this->$funcName($filePath, $level);
            }
        }

        return true;
    }

    protected function findIndexedFiles($dirName, $findValue) {
        $dirPath = $dirName . DIRECTORY_SEPARATOR . INDEXED_FILES_FNAME;
        if(!file_exists($dirPath))
            return false;

       $files = file($dirPath);

       foreach ($files as $key => $filePath) {
           $this->find($filePath, $findValue);
       }

       return true;
    }

    public function scan($searchDir = '', $searchValue = '') {

        $searchDir   = (!$searchDir) ? $this->searchDir : $searchDir;
        $searchValue = (!$searchValue) ? $this->searchValue : $searchValue;

        if($this->findIndexedFiles($searchDir, $searchValue))
             return true;

        $errorMessage = '';
        if (!is_dir($searchDir))
            $errorMessage = 'ERROR:Такая директория не существует -- ' . $searchDir;

        if (!$searchValue)
            $errorMessage = 'ERROR: Пустое значение для поиска ';

        if($errorMessage) {
            $this->errors[] = $errorMessage;
            return false;
        }

        $files = glob($searchDir ."/*");
        // $files = scandir($searchDir);

        $files = array_diff($files, array('.', '..'));

        foreach ($files as $key => $filePath) {
            if (is_file($filePath)) {
                $this->find($filePath, $searchValue);
            } else {
                $funcName = __FUNCTION__;
                $this->$funcName($filePath, $searchValue);
            }
        }
        return true;
    }

    public function find($fileName, $searchValue) {

        $fileContent = file(trim($fileName));
        $results = array();

        foreach ($fileContent as $num => $row) {

            if (!$row) continue;

            if (strpos($row, $searchValue, 0) !== false) {
                $results[$num] = array(
                    'string' => $searchValue,
                    'path'   => $fileName,
                    'num'    => $num,
                    'row'    => $row,
                );
            }
        }

        if (count($results))
            $this->results[$fileName] = $results;
    }

    protected function findString($destString, $substr) {
        $num = strpos($destString, $substr);
        if ($num !== false)
            return true;
        return false;

    }

    function fopenFind($filePath, $searchValue) {

        $matches = array();
        $handle = fopen($filePath, "r");
        if (!$handle)
            return false;

        $num = 0;
        while (!feof($handle)) {
            $num++;
            $buffer = fgets($handle);
            if (strpos($buffer, $searchValue) !== false) {
                $matches[$num] = array(
                    'search' => $searchValue,
                    'path'   => $filePath,
                    'num'    => $num,
                    'row'    => $buffer
                );
            }
        }

        fclose($handle);

        if (count($matches))
            $this->results[$filePath] = $matches;
    }


    public function getResults() {
        return $this->results;
    }

    public function getError($error = null) {
        if(!empty($this->errors) || !empty($error)) {
            print_r($this->error);
            print_r($error);
            die('ERROR!!!');
        }
    }

    protected function getFileInfo($filePath) {
        if(!file_exists($filePath)) return false;
        return array(
            'path'     => $filePath,
            'name'     => basename($filePath),
            'state'    => stat($filePath),
            'realpath' => realpath($filePath),
            'pathinfo' => pathinfo($filePath),
        );
    }

    public function findFileByName($path = '') {
        if(!$path)
          $path = $_SERVER["DOCUMENT_ROOT"];

        $directory = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($directory);

        foreach ($iterator as $info) {
            $name_file = substr($info->getfileName(), 0, strrpos($info->getfileName(), "."));
            $name_search = array("robots", "www_pandoge_com"); // Список файлов
            foreach($name_search as $key_name) {
                if($name_file == $key_name) {
                    echo $info->getPathname()."<br>";
                }
            }
        }
    }

    public function findFileByLike($path = '') {

        if(!$path)
            $path = $_SERVER["DOCUMENT_ROOT"];

        $directory = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($directory);

        foreach ($iterator as $info) {
            $name_file = substr($info->getfileName(), 0, strrpos($info->getfileName(), "."));
            $name_search = array("robots", "www_pandoge_com"); // Список файлов
            foreach($name_search as $key_name) {
                if(preg_match("/".$key_name."/", $name_file)) {
                    echo $info->getPathname()."<br>";
                }
            }
        }
    }

    public function findFileByExt($path) {

        if(!$path)
            $path = $_SERVER["DOCUMENT_ROOT"];

        $directory = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($directory);

        foreach ($iterator as $info) {
            $file_formal = substr($info->getfileName(), strrpos($info->getfileName(), ".") + 1);
            $name_search = array("mp3", "jpg"); // Список форматов
            foreach($name_search as $key_name) {
                if($file_formal == $key_name) {
                    echo $info->getPathname()."<br>";
                }
            }
        }
    }

}

function commandRunDemon($cmd) {
    $osName = php_uname();
    if (substr($osName, 0, 7) == "Windows"){
        pclose(popen("start /B ". $cmd, "r"));
    }
    else {
        exec($cmd . " > /dev/null &");
    }
}


function memoryCheck() {
    return array(
        'date'      => date('Y-m-d__H-i-s'),
        'time'      => date('H-i-s'),
        'microtime' => microtime(true),
        'memory'    => memory_get_usage(),
    );
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