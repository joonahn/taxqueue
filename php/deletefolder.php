<?php 
	
	function deleteFolder($ID)
	{
		// Check NULL string
		if ($ID === "")
		{
			echo "[ERROR] NULL STRING";
			return;
		}

		// Check directory
		if(strpos($ID, '..') !== false)
		{
			echo "[ERROR] CHDIR NOT SUPPORTED";
			return;
		}

		// Check folder existance
		if (!file_exists("../data/{$ID}"))
		{
			echo "[ERROR] FOLDER NOT EXISTS";
			shell_exec("sed -ie '/{$ID}$/d' ../data/results.txt");
			return;
		}
		
		shell_exec("rm -rf ../data/{$ID}");
	}

	function checkValidity($ID)
	{
		// Check folder existance
		if (file_exists("../data/{$ID}"))
		{
			echo "[ERROR] THE FOLDER WAS NOT DELETED";
			return;
		}
		else
		{
			shell_exec("sed -ie '/{$ID}$/d' ../data/results.txt");
		}
	}

	deleteFolder($_POST['ID']);
	checkValidity($_POST['ID']);

 ?>