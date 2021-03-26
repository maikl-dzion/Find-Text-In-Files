<?php

namespace Twofed\SearchInFile;

class SearchInFile
{
    private string $needle;

    /**
     * SearchInFile constructor
     *
     * @param $needle
     */
    public function __construct($needle)
    {
        $this->needle = $needle;
    }

    /**
     * Function to search all lines with needle in file
     *
     * @param File $currentFile
     * @return array|null
     */
    public function searchInFile(File $currentFile): ?array
    {
        $matches = array();
        $handle = @fopen($currentFile->getFileName(), "r");
        if ($handle === false) return null;
        $lineCounter = 0;

        if ($handle) {
            while (!feof($handle)) {
                $lineCounter++;
                $buffer = fgets($handle);
                if (strpos($buffer, $this->needle) !== FALSE) $matches[$lineCounter] = $buffer;
            }
            fclose($handle);
        }

        return count($matches) > 0 ? $matches : null;
    }
}
