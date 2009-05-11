<?php
/**
 * Classe que define algumas operações costumeiras, como remover arquivos de um diretório (ou o próprio diretório),
 * Compactar um diretório ou arquivo, Descompactar um arquivo compactado, Enviar um arquivo de um computador
 * para um servidor com suporte a FTP dentre várias outras funcionalidades.
 * 
 * Copyright 2009, Radig - Soluções em TI. (http://www.radig.com.br)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @author Cauan Cabral
 * @copyright Radig - Soluções em TI (http://www.radig.com.br), exceto quando explicitado
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Auto-include files with class definitions
 * 
 * @param $className
 * @return void
 */
function __autoload($className)
{
	//filename has look with 'utilities.class.php' OR 'file_transfer.class.php'
	$fileName = Utilities::underscore($className) . 'class.php';
	
	include_once($fileName);
}

class Utilities {
	
	public static $DS = '/';
	
	public function __construct()
	{
		
	}
	
/**
 * Move a file from server that's run to another server.
 * Utilize FTP conection for this, and use native FTP extension
 
 * Return true if file have been moved OR false otherwise
 * 
 * @param $file string - path and filename string
 * @param $toServer array - associative array
 * $toServer = array(
 *  'server' => 'ftp.radig.com.br',
 *  'port' => '21',
 *  'username' => 'user1',
 *  'password' => 'passwd1',
 *  'path' => 'temp_folder/images'
 * )
 * @return boolean
 */
	public function moveToServer($file, $toServer = array())
	{
		if( !is_file($file) )
			return false;
		
		if( !is_array($toServer) && count($toServer) < 3 )
			return false;
			
		
	}
	
/**
 * Delete a unique file
 * 
 * @param $file string
 * @param $overFtp boolean
 * @param $server array
 * 
 * @return boolean
 */
	public function deleteFile($file, $overFtp = false, $server = array())
	{
		if( !is_file($file) )
		{
			return false;
		}
		
		if($overFtp === false)
		{
			return unlink($file);
		}
		else
		{
			if( is_array($server) && count($server) > 2)
			{
				$ftp_conn = Utilities::connectFTP($server);
				
				return ftpDeleteFile($file, $ftp_conn);
			}
		}
		
		return false;
	}

/**
 * Delete a directory and your content
 * 
 * @param $directory string - fullpath to directory
 * @return boolean
 */
	public static function deleteDirectory($directory)
	{
		$dir = new DirectoryIterator( $directory );
		$status = true;
		
		foreach($dir as $file )
		{
			$filename = $file->getFilename();
			$fullPath = $file->getPathname();
			
			if( $file->isDir() )
			{
				$status = Utilities::deleteDirectory( $directory . Utilities::DS . $filename );
			}
			else
			{
				if( !$file->isDot() && $file->isWritable)
				{
					$status = unlink($filename);
				}
			}
			
			if( !$status )
			{
				return false;
			}
		}
		
		if(is_dir($directory))
		{
			//after delete all contents, remove the directory
			$status = rmdir($directory);
		}
		
		return $status;
	}
	
	
	//General purpose methods
	
/**
 * Connect to a FTP server
 * Utilize native FTP extension
 * 
 * @param $server array
 * $toServer = array(
 *  'server' => 'ftp.radig.com.br',
 *  'port' => '21',
 *  'username' => 'user1',
 *  'password' => 'passwd1',
 *  'path' => 'temp_folder/images',
 *  'timout' => 90
 * )
 * @return FTP stream
 */	
	public static function connectFTP($server = array())
	{
		if( is_array($server) && count($server) < 3)
		{
			return false;
		}
		/*
		 * TODO implementar a leitura do array associativo $server e a configuração da conexão ftp
		 */
		
	}

/**
 * Remove a file using FTP PHP extension
 * 
 * @param $file string - filename with fullpath in FTP server
 * @param $ftp_conn FTP Stream - FTP stream connection
 * @return boolean
 */
	public static function ftpDeleteFile($file, $ftp_conn)
	{
		/*
		 * TODO implementar a deleção do arquivo $file usando a conexão $ftp_conn
		 */
		
		return true;
	}
	
/**
 * Returns the given camelCasedWord as an underscored_word.
 *
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * 
 * @param string $camelCasedWord Camel-cased word to be "underscorized"
 * @return string Underscore-syntaxed version of the $camelCasedWord
 * @access public
 * @static
 */
	public static function underscore($camelCasedWord) {
		return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $camelCasedWord));
	}
	
}
?>