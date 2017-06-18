<?php 
	require_once __DIR__ . '/../vendor/autoload.php';
	use PhpAmqpLib\Connection\AMQPStreamConnection;

	function mylog($log_txt)
	{
		if ($log_txt !== "")
		{
			$log = fopen("../log/access.txt","a");
			fwrite($log, "[log]:".$log_txt."\r\n");  
			fclose($log);
		}
	}

	$connection = new AMQPStreamConnection('localhost', 5672, 'rabbitmq', 'password','taxqueue');
	$channel = $connection->channel();
	echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

	function makeotu($post_data) {
		// Argument filling
		$a2 = $post_data['primerseq'];
		$a3 = "";
		if(isset($post_data['checkFwd']))
			$a3 = $a3."fwd";
		if(isset($post_data['checkRev']))
			$a3 = $a3."rev";
		if(isset($post_data['checkFull']))
			$a3 = $a3."full";
		if ($a3 === "")
			$a3 = "empty";
		$a4 = $post_data['taxalg'];
		$a5 = $post_data['rdpdb'];
		$a6 = $post_data['conflevel'];
		$a7 = $post_data['trlen'];

		$count = intval($post_data['count']);
		// $randomFolder = substr_replace(sha1(microtime(true)), '', 12);
		$randomFolder = "../data/{$post_data['randomfolder']}";
		$filenames = array();
		$otutargets = "";

		for ($i = 0; $i < $count; $i++) {
			// Extract filename
			$file_path = strval($post_data[$i]);
			$file_name_only = substr($file_path,0,strrpos($file_path,"."));
			array_push($filenames, $file_name_only);
		}

		foreach ($filenames as $filename) {
			$otutargets .= " \"../data/{$filename}\"";
		}

		// Make shell arguments
		$shellarg = "{$randomFolder} {$a2} {$a3} {$a4} {$a5} {$a6} {$a7} {$otutargets}";
		debug ("shellarg: ".$shellarg);
		mylog (shell_exec("bash ../script/makeotu.sh ".$shellarg." 2>&1"));
		shell_exec("chown -R www-data:www-data {$randomFolder}");
		return $randomFolder;
	}

	function taxassn($post_data, $folder_name){
		$folder = "../data/{$folder_name}";
		// Argument filling
		$a2 = $post_data['primerseq'];
		$a3 = "";
		if(isset($post_data['checkFwd']))
			$a3 = $a3."fwd";
		if(isset($post_data['checkRev']))
			$a3 = $a3."rev";
		if(isset($post_data['checkFull']))
			$a3 = $a3."full";
		if ($a3 === "")
			$a3 = "empty";
		$a4 = $post_data['taxalg'];
		$a5 = $post_data['rdpdb'];
		$a6 = $post_data['conflevel'];
		$a7 = $post_data['trlen'];

		// Make shell arguments
		$shellarg = "{$folder} {$a2} {$a3} {$a4} {$a5} {$a6} {$a7}";

		mylog (shell_exec("bash ../script/taxassn.sh ".$shellarg." 2>&1"));
		shell_exec("chown -R www-data:www-data {$folder}");

		$toarchiveList = array($folder."/tax_output/otus_tax_assignments.txt",
			$folder."/otus.txt", $folder."/otus.fa",
			$folder."/1.txt",
			$folder."/2.txt",
			$folder."/3.txt",
			$folder."/4.txt",
			$folder."/5.txt",
			$folder."/6.txt",
			$folder."/7.txt");

		return $toarchiveList;
	}

	function archive($archive_list, $folder_name) {

		$randomName = substr_replace(sha1(microtime(true)), '', 12).'.zip';

		$cmdstr = "zip -j ../data/{$folder_name}/{$randomName}";

		foreach ($archive_list as $filename) {
			$cmdstr .= " \"{$filename}\"";
		}

		shell_exec($cmdstr);
		shell_exec("chown -R www-data:www-data ../data/{$folder_name}");
		return "./data/{$folder_name}/{$randomName}";
	}

	// check existance of
	// merged.fa, derep.fa, otus.fa, otus.txt
	// SUCCESS:	return ""
	// FAIL:	return "{$reason}"
	function makeotu_check($folder_name) {

		if (!file_exists("../data/{$folder_name}/merged.fa"))
			return "merged.fa file does not exists";

		if (!file_exists("../data/{$folder_name}/derep.fa"))
			return "derep.fa file does not exists";

		if (!file_exists("../data/{$folder_name}/otus.fa"))
			return "otus.fa file does not exists";

		if (!file_exists("../data/{$folder_name}/otus.txt"))
			return "otus.txt file does not exists";

		if (filesize ("../data/{$folder_name}/otus.txt") === 0)
			return "otus.txt file is empty";

		return "";
	}

	// check existance of
	// otus_tax_assignments.txt, 1.txt, 7.txt
	// SUCCESS:	return ""
	// FAIL:	return "{$reason}"
	function taxassn_check($folder_name) {
		if (!file_exists("../data/{$folder_name}/tax_output/otus_tax_assignments.txt"))
			return "otus_tax_assignments.txt file does not exists";

		if (!file_exists("../data/{$folder_name}/1.txt"))
			return "1.txt file does not exists";

		if (!file_exists("../data/{$folder_name}/7.txt"))
			return "7.txt file does not exists";

		return "";
	}

	function failed($taskname, $reason, $ID) {
		shell_exec("sed -ie '/^{$taskname}/d' ../data/queued.txt");
		$result_str = "{$taskname}\tfailed\t-\t{$reason}\t{$ID}";
		shell_exec("echo \"{$result_str}\" >> ../data/results.txt");
		shell_exec("chown www-data:www-data ../data/results.txt");
		shell_exec("chown www-data:www-data ../data/queued.txt");
		echo ("[{$taskname}]:\tfailed due to {$reason}");
	}

	function succeeded($taskname, $archivePath, $ID) {
		shell_exec("sed -ie '/^{$taskname}/d' ../data/queued.txt");
		$result_str = "{$taskname}\tsucceeded\t{$archivePath}\tsucceeded\t{$ID}";
		shell_exec("echo \"{$result_str}\" >> ../data/results.txt");
		shell_exec("chown www-data:www-data ../data/results.txt");
		shell_exec("chown www-data:www-data ../data/queued.txt");
		echo ("[{$taskname}]:\tsucceeded");
	}

	function debug($quote)
	{
		echo $quote."\n";
	}
	
	// Define Callback here
	$callback = function($msg) {
		echo " [x] Received ", $msg->body, "\n";
		$post_data = json_decode($msg->body, true);

		shell_exec("sed -ie '/^{$post_data['taskname']}/ s/queued/processing/' ../data/queued.txt");
		shell_exec("chown www-data:www-data ../data/queued.txt");
		$randomFolder = $post_data['randomfolder'];

		// Make OTU
		$otufolder = makeotu($post_data);
		$reason = makeotu_check($randomFolder);
		if ($reason !== "") {
			failed($post_data['taskname'], 
					"makeotu failed: {$reason}", 
					$randomFolder);
			return;
		}

		// Taxonomy Assignment
		$resultFiles = taxassn($post_data, $randomFolder);
		$reason = taxassn_check($randomFolder);
		if ($reason !== "") {
			failed($post_data['taskname'], 
					"taxassn failed: {$reason}", 
					$randomFolder);
			return;
		}

		// Archive
		$archivePath = archive($resultFiles, $randomFolder);
		if ($archivePath === "") {
			failed($post_data['taskname'], "archiving failed", $randomFolder);
			return;
		}

		// delete specific line @ queued.txt
		succeeded($post_data['taskname'], $archivePath, $post_data['randomfolder']);
		return;
	};

	$channel->basic_consume('taxqueue', '', false, true, false, false, $callback);

	while(count($channel->callbacks)) {
	    $channel->wait();
	}

	$channel->close();
	$connection->close();

 ?>