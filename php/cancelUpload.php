<?php 
	$folder_name = basename(dirname(strval($_POST["filename"])));
	shell_exec ("rm -rf ../data/{$folder_name}");
 ?>