<?php


class Dispatcher {

    protected  $request;
    protected  $server;
    protected  $action;
    protected  $fileSystem;

    protected  $submit;
    protected  $dirPath;
    protected  $findText;
    protected  $replaceText;
    protected  $file;

    public function __construct($fileSystem) {

        $this->request = $_REQUEST;
        $this->server  = $_SERVER;
        $this->fileSystem  = $fileSystem;
        $this->init();

    }

    protected function getRequest($fieldName) {
        $value = false;
        if(!empty($this->request[$fieldName]))
            $value = $this->request[$fieldName];
        return $value;
    }

//    public function init() {
//
//        // Стратегия поиска
//        $this->dirPath  = trim($this->getRequest('dir'));
//        $this->findText = trim($this->getRequest('text'));
//        $this->replaceText  = trim($this->getRequest('retext'));
//
//        if($this->getRequest('submit')) {
//            $this->action = 'submit';
//        }
//
//        // Редактирование файла
//        if($file = $this->getRequest('edit')) {
//            $this->file   = $file;
//            $this->action = 'edit';
//        }
//    }
//
//    public function run() {
//
//        $response = $memory = $result = $error = array();
//        $execTime = $fileContent = $filePath = '';
//        $manager = $this->fileSystem;
//
//        switch($this->action) {
//            case 'edit' :
//                $filePath = $this->file;
//                $type = 'text';
//                $fileContent = $manager->getFileContent($filePath, $type);
//                $error  = $manager->getError();
//
//                break;
//
//            case 'submit' :
//
//                $memory['start'] = memoryCheck();
//                $startTime = microtime(true);
//
//                $manager->scan($this->dirPath, $this->findText);
//                $response = $manager->getResponse();
//                $result = (!empty($response['result'])) ? $response['result'] : array();
//                $error  = (!empty($response['error'])) ? $response['error'] : false;
//
//                $execTime = round(microtime(true) - $startTime, 3);
//                $memory['end'] = memoryCheck();
//
//                break;
//        }
//
//
//        return array(
//            'file_content' => $fileContent,
//            'file_path'    => $filePath,
//            'path'   => $this->dirPath,
//            'text'   => $this->findText,
//            'action' => $this->action,
//            'memory'    => $memory,
//            'exec_time' => $execTime,
//            'result'    => $result ,
//            'error'     => $error,
//            'response'  => $response,
//        );
// }

}


class FileSystemController
{
    protected $results = array();
    protected $errors  = array();

    public function scan($dirPath = '', $findValue = '') {
        if(!$this->validate($dirPath, $findValue))
            return false;
        $files = glob($dirPath ."/*");
        foreach ($files as $key => $filePath) {
            $filePath = trim($filePath);
            if (is_file($filePath)) {
                $this->find($filePath, $findValue);
            } else {
                $funcName = __FUNCTION__;
                $this->$funcName($filePath, $findValue);
            }
        }
        return true;
    }

    public function getFileContent($filePath, $type = 'text') {

        if( (!file_exists($filePath)) || (!is_file($filePath)) ) {
            $this->errors[] = 'ERROR:Файл не наден -- ' . $filePath;
            return false;
        }

        if($type == 'text')
            $fileContent = htmlspecialchars(encoding(file_get_contents($filePath)));
        else
            $fileContent = file($filePath);

        return $fileContent;
    }

    public function find($fileName, $searchValue) {
        $results = array();
        $fileContent = file($fileName);
        foreach ($fileContent as $num => $row) {
            if(!$row) continue;
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

    public function getResponse() {
        return array(
            'result' => $this->results,
            'error'  => $this->errors,
        );
    }

    protected function validate($dirPath, $findValue) {
        $errorMessage = '';
        if (!is_dir($dirPath))
            $errorMessage = 'ERROR:Такая директория не существует -- ' . $dirPath;
        if (!$findValue)
            $errorMessage = 'ERROR: Пустое значение для поиска ';
        if($errorMessage) {
            $this->errors[] = $errorMessage;
            return false;
        }
        return true;
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

    public function getError() {
        return $this->errors;
    }

}


class Template {

    public function render($response) {

        $html  = $edit = $error = '';
        $action = (!empty($response['action'])) ? $response['action'] : '';

        if(!empty($response['error'])) {
            foreach ($response['error'] as $key => $value) {
                $error .= '<div style="color:red" >' .$value. '</div>';
            }
        }

        switch($action) {
            case 'edit' :
                $edit = $this->editView($response);
                break;

            case 'submit' :
                $html = $this->findResultView($response);
                break;
        }

        return array(
            'action' => $action,
            'html'   => $html,
            'edit'   => $edit,
            'error'  => $error,
        );
    }

    protected function editView($response) {
        $html = '';
        if (empty($response['file'])) {
            $html = '<div class="alert-error"><h4>Ошибка!</h4> Файл не найден </div>';
        } else {

            $fileContent = $response['file_content'];
            $filePath    = $response['file_path'];

            $html .= '<textarea id="source"> ' . $fileContent . ' </textarea>';
            $html .= '<div class="clear"></div><a class="btn btn-danger right" href="javascript:window.close()"> Закрыть </a>&nbsp;';
            $html .= '<a onClick="save(\'' . urlencode($filePath) . '\')" class="btn btn-info right" href="#" > Сохранить </a>';
        }

        return $html;
    }

    protected function findResultView($response) {

        $cnt = 0;
        $html   = '<div class="result">';
        $header = '<h2>Заданный текст найден в файлах:</h2> ';
//        if ($replace)
//            $header = '<h2>Заданный текст заменен в файлах:</h2>';

        $html .= $header;

        // lg($response);

        if(!empty($response['error'])) {

            foreach ($response['error'] as $key => $value) {
                $row = '<div>'.$value.'<div>';
                $html .= $row;
            }

        } elseif(!empty($response['result'])) {

            foreach ($response['result'] as $key => $file) {
                foreach ($file as $num => $item) {

                    $cnt++;
                    $filePath = $item['path'];
                    // $fileUrl = urlencode(dirname(__FILE__) . DIRECTORY_SEPARATOR . $filePath);
                    $fileUrl = urlencode($filePath);
                    $rowLine = htmlspecialchars($item['row']);

                    // lg([$fileUrl, $item, $filePath]);

                    $html .= '<div class="result-item" >
                                 <div class="left"><b>' . $cnt . ':</b> <span class="file">' . encoding($filePath) . '</span></div>
                                 <div onClick="del(this, \'' . $fileUrl . '\')" title="Удалить файл" class="btn btn-danger btn-mini right">x</div>
                                 <div onClick="edit(\'' . $fileUrl . '\')" class="btn btn-info btn-mini right"> Редактировать </div>
                                 <div class="clear"></div>
                                 <code title="Щелкните два раза, чтоб увидеть полный текст файла"
                                       ondblclick="seeAll(this, \'' . $fileUrl . '\')">...' . $rowLine . '...</code>
                              </div>';
                }
            }

        } else {
            $html .= 'Нет совпадений';
        }

        if(!empty($response['exec_time']))
            $html .= '<br><b>Время выполнения: ' . $response['exec_time'] . ' сек.</b></div>';

        return $html;
    }

}




//функция поиска
function scan_dir($dirname)
{

    global $text, $replace, $ext, $cnt, $html, $regex, $regis;

    $dir = opendir($dirname);

    while (($file = readdir($dir)) !== false) {

        if ($file != '.' && $file != '..') {
            $file_name = $dirname . DIRECTORY_SEPARATOR . $file;

            if (is_file($file_name)) {
                $ext_name = substr(strrchr($file_name, '.'), 1);

                if (in_array($ext_name, $ext) || $file_name == '.' . DIRECTORY_SEPARATOR . BASE_NAME) {
                    continue;
                }

                $content = encoding(file_get_contents($file_name));
                $str = '';

                if ($regex) {
                    if (preg_match('/' . $text . '/s' . $regis, $content, $res, PREG_OFFSET_CAPTURE)) {
                        $str = preg_replace('/(' . $text . ')/s' . $regis, "%find%\$1%/find%",
                            mysubstr($content, $res[0][1], $res[0][0]));
                    }
                } else {
                    if (($pos = strpos($content, $text)) !== false) {
                        $str = str_replace($text, '%find%' . $text . '%/find%', mysubstr($content, $pos, $text));
                    }
                }

                if ($str != '') {
                    $cnt++;

                    if ($replace) {
                        replace($content, $file_name, $regex);
                    }

                    $arg = urlencode(DIR_NAME . DIRECTORY_SEPARATOR . $file_name);

                    $html .= '<div class="result-item">
                                 <div class="left"><b>' . $cnt . ':</b> <span class="file">' . encoding($file_name) . '</span></div>
                                 <div onClick="del(this, \'' . $arg . '\')" title="Удалить файл" class="btn btn-danger btn-mini right">x</div>
                                 <div onClick="edit(\'' . $arg . '\')" class="btn btn-info btn-mini right">Редактировать</div>
                                 <div class="clear"></div>
                                 <code title="Щелкните два раза, чтоб увидеть полный текст файла" 
                                       ondblclick="seeAll(this, \'' . $arg . '\')">...' . htmlspecialchars($str) . '...</code>
                             </div>';
                }
            }

            if (is_dir($file_name)) {
                scan_dir($file_name);
            }
        }
    }

    closedir($dir);
}

//удаляем экранирование если нужно
function mystripslashes($string) {
    if (@get_magic_quotes_gpc()) {
        return stripslashes($string);
    } else {
        return $string;
    }
}

//функция замены
function replace($content, $file_name, $regex)
{
    global $retext, $text, $regis;

    if ($regex) {
        $content = preg_replace('/' . $text . '/s' . $regis, $retext, $content);
    } else {
        $content = str_replace($text, $retext, $content);
    }

    if (!is_writable($file_name)) {
        chmod($file_name, 0644);
    }

    return file_put_contents($file_name, $content);
}


function mysubstr($content, $pos, $find_str, $cnt_str = CNT_STR)
{

    $pos_start = $pos - $cnt_str;

    if ($pos_start <= 0) {
        $pos_start = 0;
    }

    $pos_end = ($pos - $pos_start) + strlen($find_str) + $cnt_str;

    return substr($content, $pos_start, $pos_end);
}

function encoding($content) {

    if (mb_check_encoding($content, 'windows-1251') && !mb_check_encoding($content, 'utf-8')) {
        return mb_convert_encoding($content, 'utf-8', 'windows-1251');
    } else {
        return $content;
    }

}

function tree($dirname)  {
    $html = '';
    if (is_readable($dirname)) {
        $dir = opendir($dirname);
        while (($item = readdir($dir)) !== false) {
            if ($item != '.' && $item != '..') {
                $path = $dirname . DIRECTORY_SEPARATOR . $item;
                if (is_dir($path)) {
                    $html .= '<div class="dir-list-item" ><span onclick="getDirs(this)" class="dir-open-btn" >+</span><a rel="' . $path . '" onclick="document.getElementById(\'dir\').value = this.getAttribute(\'rel\');" >  ' .$item. '</a><div class="tree-items"></div></div>';
                } else {
                    $html .= '<div class="dir-list-item" >
                                   <div style="margin-left: 20px; font-style: italic" onclick="document.getElementById(\'dir\').value = this.getAttribute(\'rel\');" >  ' .$item. '</div>
                              </div>';
                }
            }
        }
    } else {
        $html .= '<div> Директория не доступна для чтения </div>';
    }

    return $html;
}

function memoryCheck()
{
    return array(
        'date' => date('Y-m-d__H-i-s'),
        'time' => date('H-i-s'),
        'microtime' => microtime(true),
        'memory' => memory_get_usage(),
    );
}


function lg(){

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

function _filePutContents($filename, $data) {
    $file = fopen($filename, 'w');
    if (!$file)
        return false;
    $bytes = fwrite($file, $data);
    fclose($file);
    return $bytes;
}

function _fileGetContents($filename) {
    $file = fopen($filename, 'r');
    $fcontents = fread($file, filesize($filename));
    fclose($file);
    return $fcontents;
}


function scanDirectories($dirPath, $findValue) {
    $files = glob($dirPath ."/*");
    $findWorker = 'find_worker.php';
    foreach ($files as $key => $filePath) {
        $filePath = trim($filePath);
        if (is_file($filePath)) {

            $cmd = 'php '.$findWorker.' "'.$filePath.'" "'.$findValue.'"';

            // echo "<div style='margin:3px; padding:3px; border: 1px red solid;' >$filePath</div>";
            // echo "<div style='margin:3px; padding:3px; border: 1px red solid;' >$cmd</div>";

            commandInit($cmd);
        } else {
            $funcName = __FUNCTION__;
            $funcName($filePath, $findValue);
        }
    }
    return true;
}


function commandInit($cmd) {
    $osName = php_uname();
    if (substr($osName, 0, 7) == "Windows") {
        pclose(popen("start /B " . $cmd, "r"));
    } else {
        exec($cmd . " > /dev/null &");
    }
}