<?php 
	// echo nl2br(shell_exec("bash ./data.sh 2>&1"));
	$folder = $_POST['OTUTbl'];

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
	$a8 = $_POST['OTUTbl'];

	// Extract filename
	$file_name_only = substr($file,0,strrpos($file,"."));

	// Make shell arguments
	$shellarg = "{$folder} {$a2} {$a3} {$a4} {$a5} {$a6} {$a7}";

	// echo nl2br(shell_exec("bash ./data.sh ".$shellarg." 2>&1"));
	shell_exec("bash ./taxassn.sh ".$shellarg." 2>&1");
	echo $folder."/tax_output/otus_tax_assignments.txt";

 ?>