<?php
	@session_start();
	
	if(!isset($_GET['albumID'])){
		require('managePhotos.php');
	}else{
		//Setting up the database connection
		require('db_info.inc');
		$db=new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>Big Red Bears</title>
		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="js/viewPhotos.js"></script>
		<link href='http://fonts.googleapis.com/css?family=Ubuntu+Condensed' rel='stylesheet' type='text/css' />
        <link type="text/css" rel="stylesheet" href="css/mainStyle.css"/>
		<link type="text/css" rel="stylesheet" href="css/viewPhotos.css"/>
		
        
	</head>
	<body>
		<div id="top">
				<div id="topContent">
					<img src="siteImages/logo.jpg" class="logo" alt="" />
					<?php
						require('nav.php');
					?>
				</div>
				
			</div>
			<div id="middleSmall">
				<h1 id="pageTitle">
				<?php
					//Retrieving album information and displaying the album title
					$albumID= $_GET['albumID'];
					$result=$db->query('SELECT albumTitle, description, dateCreated FROM Albums WHERE albumID='.$albumID);
					$row=$result->fetch();
					$albumTitle=$row[0];
					$albumDesc=$row[1];
					$albumDate=$row[2];
					
					echo $albumTitle;
				?>	
				</h1>
			</div>
			<div class="divide">
			</div>


			<div id="main">
				<div id="contentPanel">
			
				
				<?php
					//Prepping and displaying the album date and description
					if($albumDesc==''){
						$albumDesc='No description was provided for this album.';
					}
					$year=substr($albumDate, 0,4);
					$month=substr($albumDate, 5,2);
					$day=substr($albumDate, 8,2);
					$date=$month.'/'.$day.'/'.$year;
					
					echo'
						<div id="albumInfo">
							<h3>Created On '.$date.'</h3>
							<p id="albumDesc">'.$albumDesc.'</p>';
					if(isset($_SESSION['user'])){
						echo '<p id="albumID">-&nbsp;&nbsp; Album ID: '.$albumID.'</p>';
					}
					echo '</div>';
						
					
					//Retrieving and inserting photos
					$result=$db->query('SELECT url FROM PhotosIn WHERE albumID='.$albumID);
					$positions=array('first', 'second', 'third', 'fourth', 'fifth', 'sixth');
					$counter=0;
					echo '<div class="photoRow">';
					$empty=true;
					$overallCounter = 1;
					while($row=$result->fetch()){
						$empty=false;
						$url=$row[0];
						echo '
							<div class="thumbs '.$positions[$counter].'">
								<img id="photo'.$overallCounter.'" src="photos/thumbnails/'.$url.'" alt="thumbnail" />
							</div>';
						$counter++;
						$overallCounter++;
						if($counter>=6){
							$counter=0;
							echo '</div><div class="photoRow">';
						}
						
					}
					echo'</div>';
					
					//Displays a message if there are no photos in the album
					if($empty){
						echo '
							<div id="zeroPhotos">
								<img src="siteImages/photoStack.png" alt="Photo Icon" />
								<h2>There are no photos in this album.</h2>
						';
						if(isset($_SESSION['user'])){
								echo '<a href="addPhotos.php">Add Photos</a>';
						}
						echo '</div>';					
					}
					
					//Allows only members to edit albums
					if(isset($_SESSION['user'])){
						echo'
							<div id="editButtons">
								<form id="albumEditButtons" action="deleteAlbum.php" method="post">
									<input id="editAlbum" name="editAlbum" type="button" value="Edit This Album" />';
						if(!$empty){
							echo '<input id="deleteAllPhotos" name="deleteAllPhotos" type="button" value="Delete All Photos" />';
						}
						echo'
									<input name="albumID" type="hidden" value="'.$albumID.'" />
									<input id="deleteAlbum" name="deleteAlbum" type="submit" value="Delete This Album" />
									<input id="makeAlbumChanges" name="makeAlbumChanges'.$albumID.'" type="button" value="Submit Changes" />
									<input id="cancelChanges" name="cancelChanges" type="button" value="Cancel" />
									<textarea id="descTextArea" name="descTextArea"></textarea>
								</form>
							</div>';
					}
					
				?>
					<!-- Used to give a "faded out" look to the website when the picture modal window is present -->
					<div id="fadeOut">	
					</div>
					
					<!--  The picture modal window, only visible when a thumbnail has been clicked -->
					<div id="imagePopUp">
							<div id="closePicture">
								<img src="siteImages/whiteX.png" alt="Exit Window" />
							</div>
							<div id="prevPic">
								<img src="siteImages/whiteArrowLeft.png" alt="Previous Picture" />
								<p id="prevLabel">Prev</p>
							</div>
							<div id="nextPic">
								<p id="nextLabel">Next</p>
								<img src="siteImages/whiteArrowRight.png" alt="Next Picture" />
							</div>
							<h3 id="loadingMsg">Loading...</h3>
							
							<img id="enlargedPhoto" src="" alt="Enlarged Photo" />
							
							<!--<div id="pictureDetails">
								<p id="imageDate">08/15/2012</p>
								<p id="imageCaption">
									This is text. This is text. This is text. This is text. This is text. 
									This is text. This is text. This is text. This is text. This is text.
									This is text. This is text.
								</p>
							</div>-->
							
						
					</div>	
				</div>
			</div>

			<div class="divide"></div>
			<div id="middle2">
				<div id="middle2Content">
					<?php
						include('facebook.html');
					?>
				</div>
			</div>
			<div id="bottom"></div>

			
		</div>
	</body>
</html>
<?php
	}
?>