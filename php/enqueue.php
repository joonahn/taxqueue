<?php 
	require_once __DIR__ . '/../vendor/autoload.php';
	use PhpAmqpLib\Connection\AMQPStreamConnection;
	use PhpAmqpLib\Message\AMQPMessage;


	function mylog($log_txt)
	{
		if ($log_txt !== "")
		{
			$log = fopen("../log/access.txt","a");
			fwrite($log, $log_txt."\r\n");  
			fclose($log);
		}
	}



	$connection = new AMQPStreamConnection('localhost', 5672, 'rabbitmq', 'password','taxqueue');
	$channel = $connection->channel();

	$msg = new AMQPMessage(json_encode($_POST));

	$channel->basic_publish($msg, '', 'taxqueue');

	$result_str = "{$_POST['taskname']}\tqueued";
	mylog(shell_exec("echo \"{$result_str}\" >> ../data/queued.txt"));
	
	$channel->close();
	$connection->close();
 ?>