<?php

	//Checking if the albumID exists
	if(isset($_POST['albumID'])){
		$albumID= $_POST['albumID'];
		
		
		//Setting up the database connection
		require('db_info.inc');
		$db=new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
		
		//Retrieving the urls with this album id
		$query='SELECT url FROM PhotosIn WHERE albumID='.$albumID;
		$result=$db->query($query);
		
		//Looking for photos to unlink and remove from the Photos table
		while($row=$result->fetch()){
			$url=$row[0];
			$countResult=$db->query('SELECT COUNT(albumID) FROM PhotosIn WHERE url="'.$url.'"');
			$tempRow=$countResult->fetch();
			$total=$tempRow[0];
			
			//If there is only one entry with this url in PhotosIn, then delete it
			if($total==1){
				@unlink('photos/fullSize/'.$url);
				@unlink('photos/thumbnails/'.$url);
				$db->query('DELETE FROM Photos WHERE url="'.$url.'"');
			}
		}
		
		//Deleting rows from the PhotosIn table
		$query='DELETE FROM PhotosIn WHERE albumID='.$albumID;
		$db->query($query);
	}
?>