<?php

function print_row($data) {
	$task_name = 0;
	$task_state = 1;
	$task_link = 2;
	$task_remark = 3;
	$task_id = 4;

	$result_script = "";

	$data[$task_state] = trim($data[$task_state]);
	if (!array_key_exists($task_id, $data))
	{
		$data[$task_id] = "";
	}
	$data[$task_id] = trim($data[$task_id]);

	if ($data[$task_state] === 'succeeded')
	{
		echo "<tr class='success'>";
		echo "<td>".$data[$task_name]."</td>";
		echo "<td>".$data[$task_state]."</td>";
		echo "<td><a href='".$data[$task_link]."'>".$data[$task_link]."</a></td>";
		echo "<td>".$data[$task_remark]."</td>";
		echo "<td><button class='close {$data[$task_id]}'><span aria-hidden='true'>&times;</span></button></td>";
		echo "</tr>";

		// Delete button script
		if ($data[$task_id] !== "")
		{
			$result_script .= "$('.{$data[$task_id]}')"
								.".click(function(){"
								."	$.post('./php/deletefolder.php', {'ID':'{$data[$task_id]}'})"
								.".done(function(data) {"
								."if (data!='') alert(data);"
								."loadTable();"
								."});"
								."});";
		}
	}
	else if ($data[$task_state] === 'failed')
	{
		echo "<tr class='danger'>";
		echo "<td>".$data[$task_name]."</td>";
		echo "<td>".$data[$task_state]."</td>";
		echo "<td>".$data[$task_link]."</td>";
		echo "<td>".$data[$task_remark]."</td>";
		echo "<td><button class='close {$data[$task_id]}'><span aria-hidden='true'>&times;</span></button></td>";
		echo "</tr>";

		// Delete button script
		if ($data[$task_id] !== "")
		{
			$result_script .= "$('.{$data[$task_id]}')"
								.".click(function(){"
								."	$.post('./php/deletefolder.php', {'ID':'{$data[$task_id]}'})"
								.".done(function(data) {"
								."if (data!='') alert(data);"
								."loadTable();"
								."});"
								."});";
		}
	}
	else if ($data[$task_state] === 'queued')
	{
		echo "<tr class='warning'>";
		echo "<td>".$data[$task_name]."</td>";
		echo "<td>".$data[$task_state]."</td>";
		echo "<td>-</td>";
		echo "<td>-</td>";
		echo "<td></td>";
		echo "</tr>";
	}
	else if ($data[$task_state] === 'processing')
	{
		echo "<tr class='warning'>";
		echo "<td>".$data[$task_name]."</td>";
		echo "<td>".$data[$task_state]."</td>";
		echo "<td>-</td>";
		echo "<td>-</td>";
		echo "<td></td>";
		echo "</tr>";
	}
	else 
	{
		echo "<tr>";
		echo "<td>malformed data :".$data[$task_name]."</td>";
		echo "<td>".$data[$task_state]."</td>";
		echo "<td>".$data[$task_link]."</td>";
		echo "<td>".$data[$task_remark]."</td>";
		echo "<td></td>";
		echo "</tr>";
	}
	return $result_script;
}

echo "<table><thead><tr>";
echo "<th>task name</th><th>status</th><th>link</th><th>remark</th><th></th>";
echo "</tr></thead><tbody>";

$result_script = "";
$fp = fopen("../data/results.txt","r");

while( !feof($fp) ) 
{
	$doc_data = fgets($fp);
	$data = explode("\t", $doc_data);


	if (count($data) > 3)
	{
		$result_script .= print_row($data);
	}

}

fclose($fp);

$fp = fopen("../data/queued.txt", "r");

while (!feof($fp)) {
	
	$doc_data = fgets($fp);
	$data = explode("\t", $doc_data);

	if (count($data) > 1)
	{
		$result_script .= print_row($data);
	}
}

fclose($fp);

echo "</tbody></table>";
echo "<script>";
echo $result_script;
echo "</script>";

?>