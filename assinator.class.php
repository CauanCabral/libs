<?php
/**
 * Classe que faz a inserção ou substituição de cabeçalho explicativo no inicio de um arquivo
 *
 * Copyright 2010, Radig - Soluções em TI. (http://www.radig.com.br)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Cauan Cabral
 * @copyright Radig - Soluções em TI (http://www.radig.com.br), exceto quando explicitado
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class Assinator
{
	/**
	 * Can be 'replace' or 'append'
	 *
	 * @var mode
	 */
	protected $mode = 'replace';

	/**
	 *
	 */
	protected $target = null;

	/**
	 *
	 */
	protected $fileContent = null;

	/**
	 * Itarator for specified target
	 *
	 * @var it
	 */
	protected $it = null;

	protected $defaultSignaturePatterns = array(
		'begin' => '((<\?php)(\s|\r|t)*(\/\*\*)?)',
		'body' => '(\s|.)*',
		'end' => '(\*\/)'
	);

	/**
	 * Keep in memory the new signature for target file
	 *
	 * @var newSignature
	 */
	protected $newSignature = '';

	public function __construct($target = null, $newSignature = '', $mode = 'replace')
	{
		if(!is_string($target))
		{
			trigger_error("Você deve especificar um arquivo ou diretório", E_USER_ERROR);
			return null;
		}

		$this->target = $target;

		if($this->loadSignature($newSignature) == FALSE)
		{
			trigger_error("Não foi possível abrir o arquivo passado como nova assinatura", E_USER_ERROR);
			return null;
		}
	}

	public function run()
	{
		try
		{
			if(is_file($this->target))
			{
				$this->fileContent = file_get_contents($this->target);
				return $this->applySignature(new SplFileObject($this->target, "w"));
			}

			$this->it = new RecursiveDirectoryIterator( $this->target );

			foreach( new RecursiveIteratorIterator($this->it, RecursiveIteratorIterator::SELF_FIRST) as $filename => $cur )
			{
				if( $this->validExtension( $filename ) && $cur->isFile() )
				{
					$this->fileContent = file_get_contents($filename);
					if(!$this->applySignature(new SplFileObject($cur, "w")))
					{
						trigger_error("Não foi possível aplicar a assinatura no arquivo {$filename}", E_USER_NOTICE);
					}
				}
			}
		}
		catch( Exception $e )
		{
			trigger_error("Arquivo ou diretório passada não pode ser avaliado\nExceção disparada: " . $e->getMessage(), E_USER_ERROR);
			return FALSE;
		}

		return TRUE;
	}

	private function validExtension($filename)
	{
		$extensions = '\.php|\.php5|\.php4|\.ctp';

		return ( preg_match('/'. $extensions .'/', $filename) == 1 );
	}

	private function loadSignature($target)
	{
		if(is_file($target))
			$this->newSignature = file_get_contents($target);
		else
			$this->newSignature = $target;

		return $this->newSignature;
	}

	protected function applySignature($file)
	{
		if($this->mode == 'replace' && $this->__replaceSignature($file) == FALSE)
			return FALSE;
		else if($this->mode == 'append' && $this->__appendSignature($file) == FALSE)
			return FALSE;

		return is_numeric($file->fwrite($this->fileContent));
	}

	private function __replaceSignature($file)
	{
		$pattern = '/^' . $this->defaultSignaturePatterns['begin'] . $this->defaultSignaturePatterns['body'] . $this->defaultSignaturePatterns['end'] . '/';
		$changed = preg_replace($pattern, $this->newSignature, $this->fileContent);

		if($changed == null || $changed == $this->fileContent)
		{
			$changed = preg_replace('/^' . $this->defaultSignaturePatterns['begin'] . '/', $this->newSignature, $this->fileContent);
		}

		$this->fileContent = $changed;

		return is_string($changed);
	}

	private function __appendSignature($file)
	{
		$pattern = '/^(' . $this->defaultSignaturePatterns['begin'] . $this->defaultSignaturePatterns['body'] . $this->defaultSignaturePatterns['end'] . ')/';

		$changed = preg_replace($pattern, '$1' . $this->newSignature, $this->fileContent);

		if($changed == null || $changed == $this->fileContent)
		{
			$changed = preg_replace('/^' . $this->defaultSignaturePatterns['begin'] . '/', '$1' . $this->newSignature, $this->fileContent);
		}

		$this->fileContent = $changed;

		return is_string($changed);
	}
}

$a = new Assinator('./teste/', 'teste.txt');
$a->run();
?>