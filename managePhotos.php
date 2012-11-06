<?php
	@session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>Big Red Bears</title>
		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="js/managePhotos.js"></script>
		<link href='http://fonts.googleapis.com/css?family=Ubuntu+Condensed' rel='stylesheet' type='text/css' />
        <link type="text/css" rel="stylesheet" href="css/mainStyle.css"/>
		<link type="text/css" rel="stylesheet" href="css/managePhotos.css"/>
		
        
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
				<h1 id="pageTitle">Photo Albums</h1>
			</div>
			<div class="divide">
			</div>


			<div id="main">
				<div id="contentPanel">
					<?php
						if(isset($_SESSION['user'])){
							echo '<a id="photosLink" href="addPhotos.php" title="Add Photos">Add Photos</a>';
						}
						
			
						//Setting up the database connection
						require('db_info.inc');
						$db=new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
						
						//Retrieving the cover photos for each album
						$result=$db->query('SELECT albumID FROM Albums');
						$positions=array('first', 'second', 'third');
						$counter=0;
						echo'<div class="albumsRow">';
						while($row=$result->fetch()){
							$albumID=$row[0];
							$photosInResult=$db->query('SELECT url FROM PhotosIn WHERE albumID='.$albumID);
							if($row=$photosInResult->fetch()){
								$url='photos/fullSize/'.$row[0];
							}else{
								$url='siteImages/defaultThumb.png';
							}
					
							echo'
								<div class="album '.$positions[$counter].'">
									<a href="viewPhotos.php?albumID='.$albumID.'">
										<img class="firstPhoto" src="'.$url.'" alt="Album Cover" />
									</a>
									<div class="secondPhoto"></div>
									<div class="thirdPhoto"></div>
								</div>
							';
							
							$counter++;
							if($counter>=3){
								echo '</div><div class="albumsRow">';
								$counter=0;
							}
						}
						echo '</div>';
					?>
				
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