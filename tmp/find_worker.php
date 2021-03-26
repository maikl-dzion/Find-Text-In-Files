<?php

set_time_limit(0);

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$filePath    = 'test/index.js';
$searchValue = 'encoding';
$resultFile  = 'result/result.txt';

if(!empty($argv[1]))
    $filePath = $argv[1];

if(!empty($argv[2]))
    $finValue = $argv[2];

if(!empty($argv[3]))
    $resultFile = $argv[3];

// print_r($filePath); die;

$result = find($filePath, $finValue);

// sleep(1);

if(!empty($result)) {
    file_put_contents('file_result.txt', "{$filePath} \n", FILE_APPEND | LOCK_EX);
    // saveTofile('find_result.txt', $result, $filePath);
}

function saveTofile($filePath, $data) {
    $dataString = "";
    // $dataString = "file={$filePath} \n";
    foreach ($data as $key => $item) {
        $row = '';
        foreach ($item as $fname => $value) {
            $row .= "{$fname}={$value} \n";
        }
        $dataString .= $row;
    }
    file_put_contents($filePath, "$dataString \n", FILE_APPEND | LOCK_EX);
}


function find($filePath, $findValue) {

    $_count = 0;
    $contentList = file($filePath);
    $results = array();

    foreach ($contentList as $num => $value) {
        if (!$value) continue;

        if (strpos($value, $findValue, 0) !== false) {
            $_count++;
            $results[$num] = array(
                'string' => $findValue,
                'path'   => $filePath,
                'num'    => $num,
                'line'   => $value,
            );
        }
    }

    return $results;
}