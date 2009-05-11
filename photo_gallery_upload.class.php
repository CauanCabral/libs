<?php
require_once('image_resample.class.php');
require_once('upload_file.class.php');

class PhotoGalleryUpload extends UploadFile
{
	private $resample; //guarda instancia da classe ImageResample
	public $saved_files;//um array com o nome final de todos os arquivos salvos
	
	public function __construct(&$files, $path)
	{
		parent::__construct($files);
		
		$this->saved_files = array();
		$this->path_final = $path;
		$this->resample = new ImageResample();
	}
	
	public function processUpload()
	{
		foreach ($this->files as $file)
		{
			//Find and set file format
			switch($file->type)
			{
				case 'image/jpeg':
					$file->ext = IMAGETYPE_JPEG;
					$file->sufix = '.jpg';
					break;
				case 'image/png':
					$file->ext = IMAGETYPE_PNG;
					$file->sufix = '.png';
					break;
				case 'image/gif':
					$file->ext = IMAGETYPE_GIF;
					$file->sufix = '.gif';
					break;
				default:
					$file->ext = null;
			}
			
			//caso o formato do arquivo seja conhecido e suportado (jpeg/gif/png)
			if( $file->ext !== null )
			{
				$this->resample->setImage($file->tmp_name, $file->ext); //seta qual imagem será redimencionada e seu formato				
				
				$this->insertPhoto($file);
			}

		}
		
		return $this->saved_files;
	}
	
	private function insertPhoto($file)
	{
		$success = true;//flag para indicar se um erro aconteceu. será usada para voltar estágio
		
		$filename = date('Y-m-d_H-i-s-u') . $file->sufix;
		
		//cria, redimensiona (se necessario) e salva a imagem e o thumb
		if( $this->createPhoto($filename) && $this->createThumb($filename) )
		{
			/*
			
			*/
			$this->saved_files[] = $filename;
		}
		else
		{
			$success = false;
		}
		
		//verifica se houve erros
		if(!$success)
		{
			//remove arquvos criados em caso de erro
			$this->removePhoto($filename);
			$this->removeThumb($filename);
		}
	}
	
	private function createPhoto($name, $max_size = 550, $quality = 100)
	{
		if($this->resample->haveImageSource())
		{
			$destination = $this->path_final . strtolower($name);
			
			//seto as informações para salvar a imagem grande da galeria
			$this->resample->setQuality($quality); //seta a qualidade da nova image
			$this->resample->setSize($max_size); //seta o tamanho máximo da imagem (qual for maior:vertical ou horizontal)
			
			return $this->resample->saveImage($destination);//salvo a imagem
		}
		
		return false;
	}
	
	private function removePhoto($name)
	{
		$destination = $this->path_final . strtolower($name);
		@unlink($destination);
	}
	
	private function createThumb($name, $max_size = 100, $quality = 80)
	{
		if($this->resample->haveImageSource())
		{
			$destination = $this->path_final . 'thumbs/' . strtolower($name);  
			
			//seto as informações para salvar o thumbnail da imagem
			$this->resample->setQuality($quality); //seta a qualidade da nova image
			$this->resample->setSize($max_size);
			
			return $this->resample->saveImage($destination);//salvo a imagem e retorno a resposta da função image<tipo>
		}
		
		return false;
	}
	
	private function removeThumb($name)
	{
		$destination = $this->path_final . 'thumbs/' . strtolower($name);
		@unlink($destination);
	}
	
}
?>