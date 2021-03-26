<?php

set_time_limit(0);

define('URI', getUri());
define('SCRIPT_NAME', 'Find.php');

$p1 = array(
    'value' => 'utf8(bool $utf8',
    'path' => __DIR__ . '\src\routing\Loader\Configurator\Traits\RouterTrait.php',
    'num' => 65
);


$p2 = array(
    'value' => 'getConnection(), $path) === true) {',
    'path' => __DIR__ . '\src\flysystem\src\Adapter\Ftp.php',
    'num' => 391
);

// Проверка поиска
$path      = __DIR__ . '/src';
$findValue = $p2['value'];
$action    = 'FIND_TEXT';
$findUrl   = URI . '/' . SCRIPT_NAME . '?action=' . $action . '&path=' . $path . '&value=' . $findValue;


// Проверка индексации
$path       = __DIR__ . '/src';
$action     = 'INDEX_DIRECTORY';
$indexedUrl = URI . '/' . SCRIPT_NAME . '?action=' . $action . '&path=' . $path . '&value=' . $findValue;

// print_r($url); die();

// die(__DIR__);

//$dir = __DIR__ . DIRECTORY_SEPARATOR . 'src';
//
//$find = new FindController();
//$result = $find->findFileByName($dir);
//
//die('Stop');


?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <style>
        a {
            margin:10px; padding:4px; font-size: 17px; border:1px gainsboro solid;
        }
    </style>
</head>
<body>

<div id="app" >

    <div style="margin:10px; padding:4px; border:2px green solid" >
        <div style="margin:10px; padding:4px;" >/<?=$findUrl;?></div>
        <div>
            <a href="/<?php echo $findUrl;?>" style="" >
                Тестируем поиск по директории
            </a>
        </div>
    </div>

    <div style="margin:10px; padding:4px; border:2px green solid" >
        <div style="margin:10px; padding:4px;" >/<?=$indexedUrl;?></div>
        <div>
            <a href="/<?php echo $indexedUrl;?>" style="" >
                Тестируем индексацию директории
            </a>
        </div>
    </div>

</div>

<script></script>

</body>
</html>

<?php


class FindController {

    public function findFileByName($path = '') {
        if(!$path)
            $path = $_SERVER["DOCUMENT_ROOT"];

        $directory = new \RecursiveDirectoryIterator(trim($path));
        $iterator = new \RecursiveIteratorIterator($directory);

        foreach ($iterator as $info) {
            $name_file = substr($info->getfileName(), 0, strrpos($info->getfileName(), "."));
            $name_search = array("ConnectionRuntimeException.php", "composer.json", 'api.php'); // Список файлов
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


function getUri() {
    $uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    array_pop($uri);
    return implode('/', $uri);
}


?>