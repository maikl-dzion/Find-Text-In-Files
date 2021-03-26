<?php

set_time_limit(0);

header('Content-Type: text/html; charset=utf-8');

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

define('CURRENT_DIR'  , __DIR__);
define('ROOT_DIR'     , $_SERVER['DOCUMENT_ROOT']);
define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']);

if(!empty($_GET['action'])) {

    $result = array();

    $actionName = $_GET['action'];

    switch ($actionName) {
        case 'SCAN_ROOT_DIR':
            $result = createRootDirsMenu();
            break;

        case 'GET_CHILD_DIR':
            $path = $_GET['path'];
            $result = scanDirectories($path);
            break;


        case 'DOCUMET_ROOT':
        case 'GET_ROOT_DIR':
            $result[] = $_SERVER['DOCUMENT_ROOT'];
            // die(json_encode(arry(ROOT_DIR)));
            break;

        case 'GET_FILE_CONTENT':
            $path = $_GET['path'];
            if(file_exists($path))
               $result = file($path);


            break;

        case 'FIND_TEXT':

            $path = ROOT_DIR;
            if(!empty($_GET['path']))
               $path  = $_GET['path'];

            $value = $_GET['value'];
            $find = new FindWordToFiles($value, $path);
            $findResultInfo = $find->getResults();
            //$treeDirItems   = $find->getTreeItems();
            //$indexFiles     = $find->getIndexFiles();

            if (empty($findResultInfo)) {
                $messageInfo = "Совпадений в файлах не найдено, нет результатов ";
                $findResult = array('error' => $messageInfo);
                die(json_encode($findResult));
            }

            $result = array(
                'result'     => $findResultInfo,
                //'treeItems'  => $treeDirItems,
                //'indexFiles' => $indexFiles,
            );

            break;    
    }

    die(json_encode($result));
}


//#########################################
//#########################################

class FindWordToFiles
{
    public $resultsInfo = array();
    public $treeDirItems = array();
    public $indexFiles = array();
    public $searchValue = '';
    public $errorExit = '';

    public function __construct($searchValue, $dir = '')
    {
        $this->searchValue = $searchValue;
        $this->treeDirItems = $this->run($dir);
    }

    public function run($dir)
    {
        $treeDirItems = array();

        if (!is_dir($dir)) {
            lg('Такая директория отсутствует -- ' . $dir);
        }

        $resultArr = scandir($dir);

        foreach ($resultArr as $key => $value) {

            if ($this->errorExit) {
                $this->getError();
                return $treeDirItems;
            }

            if ($value == '.' || $value == '..') {
                continue;
            }

            $sourceName = $dir . '/' . $value;
            $isFile = is_file($sourceName);
            $state = stat($sourceName);
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

    public function findWordToFile($fileName)
    {

        $searchCount = 0;
        $fileArr = file($fileName);
        $this->indexFiles[] = $fileName;

        $items = array();

        foreach ($fileArr as $key => $value) {

            if (!$value) {
                continue;
            }

            if (strpos($value, $this->searchValue, 0) !== false) {
                $searchCount++;
                $item = array(
                    'string' => $this->searchValue,
                    'path' => $fileName,
                    'num' => $key,
                    'line' => $value,
                    // 'file'   => implode(' ', $fileArr),
                );
                $items[$key] = $item;
                // $glResultArray[] = $item;
            }
        }

        if ($searchCount) {
            $this->resultsInfo[$fileName] = $items;
        }

        $fileArr = array();
    }

    public function getResults()
    {
        return $this->resultsInfo;
    }

    public function getTreeItems()
    {
        return $this->treeDirItems;
    }

    public function getIndexFiles()
    {
        return $this->indexFiles;
    }

    public function getError($error = '')
    {
        print_r($this->errorExit);
        print_r($error);
        die;
    }
}

function createRootDirsMenu($dirCondition = 'www')
{
    
    $rootArr = $currArr = $wwwArr = array();
    $rootArr = dirArrayFormatted('/');
    $currArr = dirArrayFormatted(__DIR__ . '/');
    $wwwArr  = dirArrayFormatted($_SERVER['DOCUMENT_ROOT'] . '/');

    foreach ($wwwArr as $key => $dirName) {
        $res = dirArrayFormatted($dirName . '/');
        $wwwSecond[$key] = $res;
    }

    $result = array(
        'root' => $rootArr,
        'www' => $wwwArr,
        '_dir_' => $currArr,
        'www_second' => $wwwSecond,
    );

    // lg($wwwSecond);

    return $result;
}

function dirArrayFormatted($dir = '') {

    $result = array();
    if (!$dir) return $result;

    $dirList = scandir($dir);
    
    if (empty($dirList)) return $result;

    foreach ($dirList as $key => $dirName) {
        $currentDir = $dir . $dirName;
        if ($dirName == '.' || $dirName == '..') continue;

        if (!is_dir($currentDir)) continue;
        $result[$dirName] = $currentDir;
    }

    return $result;
}

function scanDirectories($dir = '') {

    $result = array();
    if (!$dir || (!file_exists($dir))) return $result;

    $dirList = scandir($dir);

    if (empty($dirList)) return $result;

    foreach ($dirList as $key => $dirName) {
        if ($dirName == '.'  || $dirName == '..') continue;
        $currentPath = $dir . DIRECTORY_SEPARATOR . $dirName;
        $_type = (!is_dir($currentPath)) ? 'file' : 'dir';
        $item = array(
            'type' => $_type,
            'path' => $currentPath
        );
        $result[$dirName] = $item;
    }

    return $result;
}


function lg()
{

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

?>