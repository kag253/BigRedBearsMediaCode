<?php

	//Setting up the database connection
	require('db_info.inc');
	$db=new mysqli($hostname, $username, $password, $database);
	
	$success=false;
	$description='';
	$date='';
	$url=$_GET['photoUrl'].".".$_GET['fileType'];
	
	//Setting up and executing a prepared statement
	$query='SELECT dateTaken, event FROM Photos WHERE url=?';
	$stmt=$db->stmt_init();
	if($stmt->prepare($query)){
		$stmt->bind_param("s", $url);
		$stmt->execute();
	}
	
	//Retrieving the data
	if($row=$stmt->fetch()){
		$success=true;
		$date=$row[0];
		$description=$row[1];
	}
	
	echo '{"success":'.$success.', "date":"'.$date.'", "description":"'.$description.'"}';



?>