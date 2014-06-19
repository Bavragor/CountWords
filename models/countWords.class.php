<?php

require_once 'doc2txt.class.php';
require_once 'PdfParser.php';

class CountWords
{
    protected $sFilePath = '';

    public $sFileExt = '';

    public $sFileText = '';

    public $iFileWords = 0;

    public function __construct($sFile)
    {
        $this->sFilePath = $sFile;

        $this->sFileExt = $sFileExt = pathinfo($sFile, PATHINFO_EXTENSION);

        if($sFileExt == 'doc' || $sFileExt == 'docx')
        {
            $oFileConversion = new Doc2Txt($sFile);

            $this->sFileText = utf8_decode($oFileConversion->convertToText());
        }
        elseif($sFileExt == 'pdf')
        {
            $oFileConversion = new PdfParser();
            $this->sFileText = utf8_decode($oFileConversion->parseFile($sFile));
        }
        elseif($sFileExt == 'txt')
        {
            $this->sFileText = utf8_decode(file_get_contents($sFile));
        }
        else
        {
            $this->sFileText = '';
        }
    }

    public function countWords()
    {
        $this->iFileWords = $iWords = $this->count_words($this->sFileText);

        return $iWords;
    }

    protected function count_words($string)
    {
        $string = htmlspecialchars_decode(strip_tags($string));
        if (strlen($string)==0)
            return 0;
        $t = array(' '=>1, '_'=>1, "\x20"=>1, "\xA0"=>1, "\x0A"=>1, "\x0D"=>1, "\x09"=>1, "\x0B"=>1, "\x2E"=>1, "\t"=>1, '='=>1, '+'=>1, '-'=>1, '*'=>1, '/'=>1, '\\'=>1, ','=>1, '.'=>1, ';'=>1, ':'=>1, '"'=>1, '\''=>1, '['=>1, ']'=>1, '{'=>1, '}'=>1, '('=>1, ')'=>1, '<'=>1, '>'=>1, '&'=>1, '%'=>1, '$'=>1, '@'=>1, '#'=>1, '^'=>1, '!'=>1, '?'=>1); // separators
        $count= isset($t[$string[0]])? 0:1;
        if (strlen($string)==1)
            return $count;
        for ($i=1;$i<strlen($string);$i++)
            if (isset($t[$string[$i-1]]) && !isset($t[$string[$i]])) // if new word starts
                $count++;
        return $count;
    }

}