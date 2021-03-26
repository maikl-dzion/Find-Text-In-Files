<?php

namespace Twofed\SearchInFile;

class File
{
    protected string $fileName;
    protected string $fileCreated;
    protected string $fileModified;
    protected string $fileSize;
    public  string $fileType;

    /**
     * File constructor
     *
     * @param $fileName
     */
    public function __construct($fileName = null)
    {
        if($fileName)
            $this->setFile($fileName);
    }


    public function setFile($fileName)
    {
        $this->fileName     = $fileName;
        $this->fileCreated  = self::getNormalizedDate(filectime($fileName));
        $this->fileModified = self::getNormalizedDate(filemtime($fileName));
        $this->fileSize     = self::getConvertedFileSize(filesize($fileName));
        $this->fileType     = filetype($fileName);

        return $this;
    }

    /**
     * Returns file name
     *
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * Returns date when file was created
     *
     * @return string
     */
    public function getFileCreated(): string
    {
        return $this->fileCreated;
    }

    /**
     * Returns date when file was modified
     *
     * @return string
     */
    public function getFileModified(): string
    {
        return $this->fileCreated;
    }

    /**
     * Returns file size
     *
     * @return string
     */
    public function getFileSize(): string
    {
        return $this->fileSize;
    }

    /**
     * Converts Unix timestamp into human readable date
     *
     * @param int $unixTimestamp
     * @return string
     */
    private static function getNormalizedDate(int $unixTimestamp): string
    {
        return date ("d.m.Y", $unixTimestamp);
    }

    /**
     * Converts bytes into human readable file size
     *
     * @param int $bytes
     * @return string
     */
    private static function getConvertedFileSize(int $bytes): string
    {
        $result = "";
        $bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );

        foreach ($arBytes as $arItem) {
            if ($bytes >= $arItem["VALUE"]) {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", "," , strval(round($result, 2))) . " " . $arItem["UNIT"];
                break;
            }
        }
        return $result;
    }

}
