<?php
require_once('conexao.php');
require_once('validation.class.php');

try
{
	//defino o diretório que será percorrido
	$dir = new DirectoryIterator( '/home/user/public_html/site' );
	$i = 0;
	//varro o diretório procurando por arquivos acentuados
	foreach($dir as $file )
	{
		$filename = $file->getFilename();
		
		//verifica se não é um diretório, ponto ('.' ou '..') e nem uma imagem
		if(!$file->isDot() && !$file->isDir() && !preg_match('/\.jpg/', $filename))
		{
			//usa os métodos da classe Validation para formar um nome de arquivo funcional
			$filename_normalized = Validation::normalize($filename, false);
			$final_filename = Validation::removeRepeatedChars($filename_normalized, false, true);
			
			//operações no banco (caso o arquivo esteja vinculado ao banco)
			$sql = 'SELECT `id` FROM `arquivos` WHERE `arq1` = \''.$filename.'\'';
			$r = mysql_query($sql) or die('Problema na query do bd<br />'.mysql_error());
			$db = mysql_fetch_object($r);
			echo '<br />---------------------------------------------<br />';
			
			echo "Nome: $filename - ID no banco: $db->id<br />Nome final: $final_filename<br />Posso escrever: ".$dir->isWritable();
			
			//Altero os arquivos agora e seu valor no bd
			if($db->id && rename('../uploads/'.$filename, '../uploads/'.$final_filename))
			{
				$sql = 'UPDATE `amarildo_amarildo`.`arquivos` SET `arq1` = \''.$final_filename.'\' WHERE `arquivos`.`id` = '.$db->id.' LIMIT 1';
				if(mysql_query($sql))
				{
					echo "<br />Arquivo renomeado com sucesso!";
				}
				else
				{
					echo "<br />Não foi possível atualizar o banco";
				}
			}
			else
			{
				echo "<br />Não foi possível renomear o arquivo $filename <br />";
			}
			
			$i++;
		}
	}
	
	echo "<br /><h2>Total de arquivos: $i</h2>";
}
catch(Exception $e) {
	echo $e->getMessage();
	exit;	
}