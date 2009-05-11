<?php

class XML
{
	/**
	 * External XML filename
	 * 
	 * @var string
	 */
	private $file;
	/**
	 * Object with content of XML file
	 * 
	 * @var SimpleXmlElement
	 */
	private $content;
	/**
	 * Indicate if the file can be override
	 * 
	 * @var boolean
	 */
	private $override;
	
	
	public function __construct($filename, $override = true)
	{
		if( is_string($filename) && preg_match('/.+(\.[xml])/', $filename) )
		{
			
		}
		else
		{
			
		}
	}
	
	/**
	 * Read a XML file and save the content in class attribute
	 * 
	 * @return boolean
	 */
	public function read()
	{
		$this->content = simplexml_load_file($this->file);
		
		return (isset($this->content) && $this->content);
	}
	
	/**
	 * Save a XML file with the content of $content attribute
	 * Use the attribute $filename like name
	 * 
	 * @return boolean
	 */
	public function save()
	{
		if(!$this->override && file_exists($this->file))
		{
			$filename = $this->file;
			
			//Create a new file
			for($i = 1; file_exists($filename.$i.'.xml'); $i++);
			
			$r = file_put_contents($filename, $this->content);
		}
		else
		{
			$r = file_put_contents($this->file, $this->content);
		}
		
		return $r > 0;
	}
}
?>