<?php 
	$count = intval($_POST['count']);
	$randomName = substr_replace(sha1(microtime(true)), '', 12).'.zip';
	$filenames = array();
	$cmdstr = "zip -j {$randomName}";

	for ($i = 0; $i < $count; $i++) {
		array_push($filenames, strval($_POST[$i]));
	}

	foreach ($filenames as $filename) {
		$cmdstr .= " \"{$filename}\"";
	}

	shell_exec($cmdstr);
	echo "{$randomName}";
 ?>