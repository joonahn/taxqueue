<?php

function print_row($data) {
	$task_name = 0;
	$task_state = 1;
	$task_link = 2;
	$task_remark = 3;

	$data[$task_state] = trim($data[$task_state]);

	if ($data[$task_state] === 'succeeded')
	{
		echo "<tr class='success'><td>".$data[$task_name]."</td>";
		echo "<td>".$data[$task_state]."</td>";
		echo "<td><a href='".$data[$task_link]."'>".$data[$task_link]."</a></td>";
		echo "<td>".$data[$task_remark]."</td></tr>";
	}
	else if ($data[$task_state] === 'failed')
	{
		echo "<tr class='danger'><td>".$data[$task_name]."</td>";
		echo "<td>".$data[$task_state]."</td>";
		echo "<td>".$data[$task_link]."</td>";
		echo "<td>".$data[$task_remark]."</td></tr>";
	}
	else if ($data[$task_state] === 'queued')
	{
		echo "<tr class='warning'><td>".$data[$task_name]."</td>";
		echo "<td>".$data[$task_state]."</td><td>-</td><td>-</td></tr>";
	}
	else if ($data[$task_state] === 'processing')
	{
		echo "<tr class='warning'><td>".$data[$task_name]."</td>";
		echo "<td>".$data[$task_state]."</td><td>-</td><td>-</td></tr>";
	}
	else 
	{
		echo "task state: ".$data[$task_state];
		echo "<tr><td>".$data[$task_name]."</td>";
		echo "<td>".$data[$task_state]."</td>";
		echo "<td>".$data[$task_link]."</td>";
		echo "<td>".$data[$task_remark]."</td></tr>";
	}
}

echo "<table><thead><tr>";
echo "<th>task name</th><th>status</th><th>link</th><th>remark</th>";
echo "</tr></thead><tbody>";

$fp = fopen("../data/results.txt","r");

while( !feof($fp) ) 
{
	$doc_data = fgets($fp);
	$data = explode("\t", $doc_data);


	if (count($data) > 3)
	{
		print_row($data);
	}

}

fclose($fp);

$fp = fopen("../data/queued.txt", "r");

while (!feof($fp)) {
	
	$doc_data = fgets($fp);
	$data = explode("\t", $doc_data);

	if (count($data) > 1)
	{
		print_row($data);
	}
}

fclose($fp);

echo "</tbody></table>";

?>