<?php

class Descompress
{
	var $zip = null;
	var $destination = '/';
	var $DS = '/';
	
	function Descompress($file, $destination, $systemType = 'unix')
	{
		$this->zip = new ZipArchive();
		
		if(is_file($file))
		{
			$this->file = $file;
		}
		
		if($systemType == 'dos')
		{
			$this->DS = '\\';
		}
		
		if(is_dir($destination))
		{
			$this->destination = $destination;
		}
	}
	
	function extract()
	{
		if ($this->zip->open($this->file) === TRUE)
		{
			$this->zip->extractTo($this->destination);
			$this->zip->close();

			return true;
		} else {
			return false;
		}
	}
}
?>
