<?php
class UploadFile
{
	protected $numberOfElements;
	protected $path_tmp;
	protected $path_final;
	protected $files;
	public $errors;
	
	public function __construct(&$files)
	{
		if(is_array($files) && !empty($files))
		{
			$this->numberOfElements = count($files['name']);//conta o número de elementos do array
			$this->files = array();
			$this->errors = array();
			
			for($i = 0; $i < $this->numberOfElements; $i++)
			{
				$obj = null;//reset value of $obj
				if(!empty($files['name'][$i]))
				{
					$obj->name = $files['name'][$i];
					$obj->type = $files['type'][$i];
					$obj->size = $files['size'][$i];
					$obj->tmp_name = $files['tmp_name'][$i];
					$obj->error = $files['error'][$i];
					
					$this->files[] = $obj;
				}
			}
		}
	}
	/**
	 * Método que salva os arquivos que estão no diretório temporário de upload
	 * em algum diretório passado como parâmetro
	 *
	 * @param string $path
	 * @return boolean TRUE se tudo ocorrer bem, FALSE caso contrário
	 */
	public function saveFile($path)
	{
		if(is_dir($path))
		{
			$this->path_final = $path;
			
			try
			{
				foreach($this->files as $files)
				{
					if(!move_uploaded_file($files['tmp_name'], $files['name']))
						throw new Exception('Erro ao mover arquivo.');
				}
			}
			catch (Exception $e) {
				$this->errors[] = 'Exception found: '.  $e->getMessage() ."\n";
				return false;
			}
		}
		else
		{
			$this->errors[] = 'PATH \''.$path.'\' don\'t is a directory ';
			return false;
		}
			
		return true;
	}
	
	/**
	 * Método que dá a opção de se salvar um arquivo de upload com novo nome
	 * Os nomes devem ser passados em um array associativo com a seguinte estrutura:
	 * $names = array('original_name' => 'new_name', 'original_name2' => 'new_name2');
	 * 
	 * E $path deve ser o caminho da pasta destino. Pode ser relativo ou completo. 
	 *
	 * @param string $path
	 * @param array $names
	 */
	public function renameFile($names)
	{
		if(is_array($names) && !empty($names))
		{			
			foreach($this->files as $file => $values)
			{
				foreach($names as $name => $new_name)
					if($values['name'] === $name)
						$this->files[$file] = $new_name;
			}
		}
		else
		{
			$this->errors[] = 'Param NAMES is empty or not a Array';
			return false;
		}
	}
	
	/**
	 * Método getter que retorna os valores enviados via upload
	 *
	 * @return array dados dos arquivos
	 */
	public function getFiles()
	{
		return $this->files;
	}
}
?>