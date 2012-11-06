<?php
	
	//Checking if the variables exist
	if(isset($_POST['albumID']) && isset($_POST['description'])){
		$albumID= $_POST['albumID'];
		$description=$_POST['description'];
		
		//Setting up the database connection
		require('db_info.inc');
		$db=new mysqli($hostname, $username, $password, $database);
		
		$success=false;
		
		//Setting up and executing a prepared statement
		$query='UPDATE Albums SET description=? WHERE albumID=?';
		$stmt=$db->stmt_init();
		if($stmt->prepare($query)){
			$stmt->bind_param("si", $description, $albumID);
			$result=$stmt->execute();
		}
		
		//Retrieving the data
		if($result){
			$success=true;
		}
		
		echo '{"success":"'.$success.'", "description":"'.$description.'"}';
	
	}

?>