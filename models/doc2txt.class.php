<?php

class Doc2Txt {
	private $filename;
	
	public function __construct($filePath) {
		$this->filename = $filePath;
	}
	
	private function read_doc()	{
        /*$fileHandle = fopen($this->filename, "r");
        $line = @fread($fileHandle, filesize($this->filename));
        $lines = explode(chr(0x0D),$line);
        $outtext = "";
        foreach($lines as $thisline)
        {
            $pos = strpos($thisline, chr(0x00));
            if (($pos !== FALSE)||(strlen($thisline)==0))
            {
            } else {
                $outtext .= $thisline." ";
            }
        }
        $outtext = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/","",$outtext);
        return $outtext;*/
        $filename = $this->filename;
        if ( file_exists($filename) )
        {
            if ( ($fh = fopen($filename, 'r')) !== false )
            {
                $headers = fread($fh, 0xA00);
# 1 = (ord(n)*1) ; Document has from 0 to 255 characters
                $n1 = ( ord($headers[0x21C]) - 1 );
# 1 = ((ord(n)-8)*256) ; Document has from 256 to 63743 characters
                $n2 = ( ( ord($headers[0x21D]) - 8 ) * 256 );
# 1 = ((ord(n)*256)*256) ; Document has from 63744 to 16775423 characters
                $n3 = ( ( ord($headers[0x21E]) * 256 ) * 256 );
# (((ord(n)*256)*256)*256) ; Document has from 16775424 to 4294965504 characters
                $n4 = ( ( ( ord($headers[0x21F]) * 256 ) * 256 ) * 256 );
# Total length of text in the document
                $textLength = ($n1 + $n2 + $n3 + $n4);
                $extracted_plaintext = fread($fh, $textLength);
# if you want the plain text with no formatting, do this
//echo $extracted_plaintext;
# if you want to see your paragraphs in a web page, do this
                return utf8_encode(nl2br($extracted_plaintext));
            }
        }
	}

	private function read_docx(){

		$striped_content = '';
		$content = '';

		$zip = zip_open($this->filename);

		if (!$zip || is_numeric($zip)) return false;

		while ($zip_entry = zip_read($zip)) {
			if (zip_entry_open($zip, $zip_entry) == FALSE) continue;

			if (zip_entry_name($zip_entry) != "word/document.xml") continue;

            while ($data = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry))) {
                $content.= nl2br($data);
            }

			zip_entry_close($zip_entry);
		}// end while

		zip_close($zip);

		$content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
		$content = str_replace('</w:r></w:p>', "\r\n", $content);
		$striped_content = strip_tags($content);

		return $striped_content;
	}

	public function convertToText() {
		if(isset($this->filename) && !file_exists($this->filename)) {
			return "File Not exists";
		}
		
		$fileArray = pathinfo($this->filename);
		$file_ext  = $fileArray['extension'];
		if($file_ext == "doc" || $file_ext == "docx")
		{
			if($file_ext == "doc") {
				return $this->read_doc();
			} else {
				return $this->read_docx();
			}
		} else {
			return "Invalid File Type";
		}
	}
}
?>