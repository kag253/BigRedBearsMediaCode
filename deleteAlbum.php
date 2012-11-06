<?php
	@session_start();
	
	//Checking to see if the user is allowed on this page
	if(isset($_SESSION['user']) && isset($_POST['deleteAlbum'])){
		$albumID=$_POST['albumID'];
		
		//Setting up the database connection
		require('db_info.inc');
		$db=new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
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
				
				//Escaping special charaters so that query and unlink works
				//$escapedUrl=addSlashes($url);
				//$escapedUrl=str_replace("-", "\-", $escapedUrl);
				
				@unlink('photos/fullSize/'.$url);
				@unlink('photos/thumbnails/'.$url);
				$db->query('DELETE FROM Photos WHERE url="'.$url.'"');
			}
		}
		
		//Deleting rows from the PhotosIn table
		$query='DELETE FROM PhotosIn WHERE albumID='.$albumID;
		$db->query($query);
		
		//Deleting the album itself
		$query='DELETE FROM Albums WHERE albumID='.$albumID;
		$db->query($query);
	
	
		require('managePhotos.php');
	}else{
		require('index.php');
	}



?>