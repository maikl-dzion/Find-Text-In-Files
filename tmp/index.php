<?php

namespace Twofed\SearchInFile;

use RecursiveDirectoryIterator, RecursiveIteratorIterator;

include 'File.php';
include 'SearchInFile.php';

date_default_timezone_set('Etc/GMT-3');

if (php_sapi_name() !== 'cli')
    throw new \Exception("Only CLI PHP available");

// $needle = $argv[1];
// $needle = ':getConv';

$needle = "Autoloader::StPutFile";

if ($needle == "")
    throw new \Exception("No argument");

// $dirName = dirname(__DIR__) . DIRECTORY_SEPARATOR;
$dirName    = __DIR__ . DIRECTORY_SEPARATOR;
$searcher   = new SearchInFile($needle);
$fileHandle = new File();

searchRun($dirName, $searcher, $fileHandle);


function searchRun($dirName, $searcher, $fileHandle) {

    $files      = getFilesInDirectory($dirName);
    $totalCount = 0;
    $buffer     = [];


    foreach ($files as $name) {

        $currentFile = $fileHandle->setFile($name);

        if($currentFile->fileType == 'dir') {

            $funcName = __FUNCTION__;
            $funcName($name, $searcher, $fileHandle);

        } else {

            $result      = $searcher->searchInFile($currentFile);
            if ($result) {

                echo sprintf("Строка была найдена в файле %s\n" , $currentFile->getFileName());
                echo sprintf("Число попаданий в файле: %s\n"    , count($result));
                $totalCount += count($result);
                echo "\n";

                array_push($buffer, sprintf("name: %s\n", $currentFile->getFileName()));

                array_walk($result, function($line, $key) {
                    array_push($GLOBALS['buffer'], sprintf("line: %s\ncontent: %s", $key, $line));
                });

                array_push($buffer, sprintf("info: %s, %s, %s\n\n",
                    $currentFile->getFileSize(),
                    $currentFile->getFileCreated(),
                    $currentFile->getFileModified()));
            }
        }

    }

    foreach ($buffer as $logLine)
        file_put_contents('./log_' . date("d.m.Y") . '.log', $logLine, FILE_APPEND);

    if($totalCount) {
        echo sprintf("Общее число строк: %s\n", $totalCount);
        echo sprintf("Данные записаны в файл %s\n", 'log_' . date("d.m.Y") . '.log');
    }

    return true;
}

function getFilesInDirectory($dirName) {
    $files = glob($dirName . '*');
    return $files;
}

function getFilesInIterator($dirName) {
    $files  = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(realpath($dirName),
        RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST);
    return $files;
}

function lg(...$data) {
    print_r($data); die;
}
