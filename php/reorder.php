<?php 

	$count = intval($_POST['count']);
	$randomFolder = $_POST['randomfolder'];

	shell_exec("mkdir ../data/{$randomFolder}");

	for ($i = 0; $i < $count; $i++) {
		// Extract filename
		$file_path = strval($_POST[$i]);
		$folder_name = basename(dirname(strval($_POST[$i])));
		echo nl2br (shell_exec("mv ../data/{$file_path} ../data/{$randomFolder} 2>&1"));
		echo nl2br (shell_exec("rm -rf ../data/{$folder_name}"));
	}



 ?>