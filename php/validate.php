<?php 

function validate_taskname($fp, $tname)
{
	while( !feof($fp) ) 
	{
		$doc_data = fgets($fp);
		$data = explode("\t", $doc_data);

		if ($data[0] === $tname)
			return false;
	}

	return true;
}
	$fp = fopen("../data/results.txt","r");
	$fp2 = fopen("../data/queued.txt", "r");

	if (validate_taskname($fp, $_POST['taskname']) &&
		validate_taskname($fp2, $_POST['taskname']))
	{
		echo "Good";
	}
	else
	{
		echo "Bad";
	}

	fclose($fp);
	fclose($fp2);
 ?>