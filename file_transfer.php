<?php
$ftp_server = 'ftp.host.com';
$ftp_user_name = 'user';
$ftp_user_pass = 'pass'; 

try
{
	// set up basic connection
	$conn_id = ftp_connect($ftp_server); 
	
	// login with username and password
	$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 
	
	// check connection
	if ((!$conn_id) || (!$login_result)) {
		echo "FTP connection has failed!";
		echo "Attempted to connect to $ftp_server for user $ftp_user_name";
		exit;
	} else {
		echo "Connected to $ftp_server, for user $ftp_user_name";
	}
	
	$path_from = './path_from/';
	$path_to = 'public_html/path_to/';
	
	//defino o diretório que será percorrido
	$dir = new DirectoryIterator( $path_from );
	
	//varro o diretório procurando por arquivos para envialos
	foreach($dir as $file )
	{
		$filename = $file->getFilename();
		
		if(!$file->isDot() && !$file->isDir())
		{			
			// upload the file
			$upload = ftp_put($conn_id, $path_to.$filename, $path_from.$filename, FTP_BINARY); 
			
			// check upload status
			if (!$upload) {
				echo "FTP upload has failed!";
			}
			else
			{
				echo "Uploaded $filename";
			}
		}
	}
	
	// close the FTP stream 
	ftp_close($conn_id); 
}
catch(Exception $e) {
	echo $e->getMessage();
	exit;	
}
?>