<?php 
	require_once __DIR__ . '/../vendor/autoload.php';
	use PhpAmqpLib\Connection\AMQPStreamConnection;
	use PhpAmqpLib\Message\AMQPMessage;

	$connection = new AMQPStreamConnection('localhost', 5672, 'rabbitmq', 'password','taxqueue');
	$channel = $connection->channel();

	$msg = new AMQPMessage(json_encode($_POST));

	$channel->basic_publish($msg, '', 'taxqueue');

	$result_str = "{$_POST['taskname']}\tqueued";
	shell_exec("echo \"{$result_str}\" >> ../data/queued.txt");
	
	$channel->close();
	$connection->close();
 ?>