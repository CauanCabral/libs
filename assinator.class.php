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
		'begin' => '/(<\?php)(\s|\r|t)*(\/\*\*)?/',
		'end' => '/\*\//'
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
				$this->fileContent = file_get_content($this->target);
				return $this->applySignature(new SplFileObject($this->target, "w"));
			}

			$this->it = new RecursiveDirectoryIterator( $this->target );

			foreach( new RecursiveIteratorIterator($this->it, RecursiveIteratorIterator::SELF_FIRST) as $filename => $cur )
			{
				if( $this->validExtension( $filename ) && $cur->isFile() )
				{
					$this->fileContent = file_get_contents($this->target);
					if(!$this->applySignature($cur))
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
		// reset cursor
		$file->rewind();

		$positions = $this->__identifyOldSignature($file);

		if($this->mode == 'replace')
			return $this->__replaceSignature($file, $positions['init'], $positions['end']);
		else if($this->mode == 'append')
			return $this->__appendSignature($file, $positions['end']);
	}

	private function __identifyOldSignature($file)
	{
		$positions = array('init' => 0, 'end' => 0);

		$begin = FALSE;
		$end = FALSE;
		
		if($file->eof())
			return $positions;
		
		while(!$file->eof() && !$end)
		{
			$current = $file->fgets();

			if(!$begin)
			{
				if(preg_match($this->defaultSignaturePatterns['begin'], $current) == 1)
				{
					$begin = TRUE;
					$positions['init'] = $file->key();
				}
			}
			
			if(preg_match($this->defaultSignaturePatterns['end'], $current) == 1)
			{
				$end = TRUE;
				$positions['end'] = $file->key();
			}
		}

		return $positions;
	}

	private function __replaceSignature($file, $initPos, $endPos)
	{
		echo "Vou apagar o que está entre {$initPos} e {$endPos}\n";

		// first, remove old signature
		$file->seek($initPos);
		while(!$file->eof() && $file->key() <= $endPos)
		{
			echo "Apagando a linha {$file->key()} que tinha o valor {$file->current()}\n";
			$file->fwrite("");
			$file->fflush();
			$file->next();
		}
		
		$file->rewind();

		echo file_get_contents("outro.php");
		
		return $this->__appendSignature($file, $initPos);
	}

	private function __appendSignature($file, $initPos)
	{
		$this->__allocFileSpace($file, $initPos);
		
		$file->seek($initPos);
		echo "A linha {$initPos} contém {$file->current()}\n";

		//$out = $file->fwrite($this->newSignature);

		return is_numeric($out = 0);
	}

	private function __allocFileSpace($file, $initPos)
	{
		$file->seek($initPos);
		
		for($neededSpace = mb_strlen($this->newSignature, '8bit'); !$file->eof() && $neededSpace < 0; --$neededSpace)
		{
			$file->fwrite(" ");
		}
		echo "Agora eu estou em {$file->ftell()}\n";

		$file->rewind();
	}
}

$a = new Assinator('outro.php', 'teste.txt');
$a->run();
?>