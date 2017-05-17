<?php 

	// Argument filling
	$a2 = $_POST['primerseq'];
	$a3 = "";
	if(isset($_POST['checkFwd']))
		$a3 = $a3."fwd";
	if(isset($_POST['checkRev']))
		$a3 = $a3."rev";
	if(isset($_POST['checkFull']))
		$a3 = $a3."full";
	if ($a3 === "")
		$a3 = "empty";
	$a4 = $_POST['taxalg'];
	$a5 = $_POST['rdpdb'];
	$a6 = $_POST['conflevel'];
	$a7 = $_POST['trlen'];


	$count = intval($_POST['count']);
	$randomFolder = substr_replace(sha1(microtime(true)), '', 12);
	$filenames = array();
	$otutargets = "";

	for ($i = 0; $i < $count; $i++) {
		// Extract filename
		$file_path = strval($_POST[$i]);
		$file_name_only = substr($file_path,0,strrpos($file_path,"."));
		array_push($filenames, $file_name_only);
	}

	foreach ($filenames as $filename) {
		$otutargets .= " \"{$filename}\"";
	}


	// Make shell arguments
	$shellarg = "{$randomFolder} {$a2} {$a3} {$a4} {$a5} {$a6} {$a7} {$otutargets}";

	// echo nl2br(shell_exec("bash ./data.sh ".$shellarg." 2>&1"));
	(shell_exec("bash ./makeotu.sh ".$shellarg." 2>&1"));
	echo $randomFolder;

 ?>