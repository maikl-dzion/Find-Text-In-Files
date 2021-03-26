<?php

set_time_limit(300);
header('Content-Type: text/html; charset=utf-8');
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// print_r($_SERVER); die;

// ВХОДНЫЕ ДАННЫЕ  
$searchDir = __DIR__;
$searchDirectory = __DIR__ . '/DSAG/src';
define('SEARCH_DIRECTORY', $searchDirectory);
define('WEB_URL', $_SERVER['PHP_SELF']);
$SEARCH_TEXT = 'v-date-picker';

// ВЫПОЛНЕНИЕ 
$html = '';

if(!empty($_GET['path'])) {
    $path =  $_GET['path'];
    $resp = renderFile($path);
    $html = $resp['html'];
} else {
    $results = searchFiles(SEARCH_DIRECTORY, $SEARCH_TEXT); 
    $resp =  renderHtml($results);  
    $html = $resp['html'];
}


/////////////////////////////////////////////////////////
echo '<a href="' . WEB_URL . '" >На главную</a> <p>';
echo $html;
/////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
/////// РЕАЛИЗАЦИЯ В ПРОЦЕДУРАХ /////////////////////////


function searchFiles($dir, $searchText, $params = array()) {
    $files   = scandir($dir); // Получаем все html-файлы из директории
    $results = array();  // Создаём массив для результатов поиска
    $funcName = __FUNCTION__;
    //print_r($files); die;

    foreach($files as $key => $file) {
        if($file == '.' || $file == '..') continue;

        $path = $dir . '/' . $file; 
        if(is_dir($path)) {
            $subResult = $funcName($path, $searchText, $params);
            $results = array_merge ($results, $subResult);
        } else {
            $item = findText($path, $searchText, $file) ;
            if(!empty($item)) {
                $results[] = $item; 
            }
       }   
    }

    return $results; // Возвращаем результат
}

function findText($path, $searchText, $fileName) {
    
    $fileContent   = file_get_contents($path);      // Помещаем содержимое файлов в переменную
    if(!$fileContent) return false;

    $count = substr_count($fileContent, $searchText); // Ищем количество вхождений искомой строки в файл
    if(!$count) return false;

    // $fileArr = file($path);
    $fileLines = file($path);
    $linesNum = array();

    foreach ($fileLines as $num => $line) {
        $pos = strpos($line, $searchText);
        if ($pos !== false) {
            $lineItem = array(
                'num'  => $num,
                'line' => $line
            );
            $linesNum[] = $lineItem;
        }
    }

    $item = array(
        'name'  => $fileName,
        'path'  => $path,
        'count' => $count,
        'lines' => $linesNum,
        'content' => '',
    );
    
    return $item;
}

function renderHtml($result, $args = array()) {
    ob_start();
    $r = array();
    foreach($result as $key => $item) { 
       $line = print_r($item, true); 
       $name = $item['name'];
       $path = $item['path'];
       $count = $item['count'];
       $getParam = '?path=' . $path;
       // die(WEB_URL . $getParam);
       $r[] =  '<div><pre>' . $line. '</pre></div>'; ?>

        <ul class="item-box" >
           <li><?=$name;?></li>
           <li><a href="<?=WEB_URL . $getParam;?>" ><?=$path;?></a></li>
           <?php foreach($item['lines'] as $i => $content) { ?>
                <ul class="line-box" >
                    <li><?=$content['num'];?></li>
                    <li><?=$content['line'];?></li>
                </ul>
           <?php } ?>
        </ul><hr> 

    <?php }
    $html = ob_get_contents();	
    ob_end_clean();
    return array('html' =>$html, 'r' =>  $r);
}

function renderFile($path) {
    $lines = file($path);
    $html = ''; 
    foreach($lines as $key => $line) { 
       // print_r($item); die(); 
       //$line = print_r($item, true); 
       $html .=  '<div><pre>' . $line. '</pre></div>'; 
    }
    return array('html' =>$html);
}


// $item = array(
//     'name'  => '',
//     'path'  => '',
//     'count' => '',
//     'lines' => '',
//     'content' => '',
// );