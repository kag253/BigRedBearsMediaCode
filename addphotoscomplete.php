<?php
	@session_start();
	
	//Checking if the user should be allowed on this page
	if(!isset($_SESSION['user'])){
		require('index.php');
	}else if(!isset($_POST['submitButton'])){
		//
		// Add Session variables here?
		//
		require('addPhotos.php');
	}else{
		require('imageResizer.php');
		//Setting up the database connection
		require('db_info.inc');
		$db=new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
		
		//Checking for empty album titles and sql injections if a new album is being created
		$fail=false;
		if($_POST['albumSelect']=='Create A New Album' && $_POST['albumTitle']==''){
			$fail=true;
			$_SESSION['photoCompleteError']='You Forgot The Album Title!';
		}else if($_POST['albumSelect']=='Create A New Album'){
		
			//Getting the current date
			date_default_timezone_set("America/New_York");
			$day=date("d");
			$month=date("m");
			$year=date("Y");
			$fullDate=$year.'-'.$month.'-'.$day;
			
			//Checking for SQL injections on the album title and description
			$db->beginTransaction();
			$query='INSERT INTO Albums(albumTitle, dateCreated, description) VALUES (?, "'.$fullDate.'", ?)';
			$stmt=$db->prepare($query);
			$stmt->bindParam(1, $_POST['albumTitle']);
			$stmt->bindParam(2, $_POST['albumDes']);
			if(!($stmt->execute())){
				$fail=true;
				$_SESSION['photoCompleteError']='Invalid Album Title Or Description!';
				$db->rollback();
			}else{
				$albumID=$db->lastInsertId();
				$db->commit();
			}
			$stmt->closeCursor();
		}else{
			$index=strpos($_POST['albumSelect'], '-');
			$albumID=intval(substr($_POST['albumSelect'], 0, $index-1));
		}
		
		
		//If there are any errors, redirect to addPhotos.php, otherwise
		//add the photos and/or album to the database
		if($fail){
			require('addPhotos.php');
		}else{
		
		//Resizing the photos and moving them from temp storage
		//to the photos folder
		$results=array();
		foreach($_FILES as $key=>$value){
		
			//Adjusting for different input tags (multiple or not)
			if($key!=''){
			
				for($i=0; $i<count($_FILES[$key]['name']); $i++){
					//echo 'the count is: '.count($value);
					$uFilename=str_replace('.', '_', $_FILES[$key]['name'][$i]);
					$uFilename=str_replace(' ', '_', $uFilename);
					
					//Checking that the status marker is set to true before adding
					//False status markers mean the user wants to delete this photo 
					//from the add process
					if($_POST[$uFilename]=='true'){
						move_uploaded_file($_FILES[$key]['tmp_name'][$i], 'photos/fullSize/'.$_FILES[$key]['name'][$i]);
						$resizeResults=resizeImage($_FILES[$key]['name'][$i], 'photos/fullSize/', 'photos/fullSize/', true, 630, 840);
						
						//Checking for errors, if none then the photo is inserted in the database
						if(!$resizeResults[0]){
							$results[''.$_FILES[$key]['name'][$i]]= $resizeResults[1];
							@unlink('photos/fullSize/'.$_FILES[$key]['name'][$i]);
						}else{
							$results[''.$_FILES[$key]['name'][$i]]= 'good';
							$resizeResults=resizeImage($_FILES[$key]['name'][$i], 'photos/fullSize/', 'photos/thumbnails/', true, 150, 150);
							$db->query('INSERT INTO Photos(url, dateTaken) VALUES("'.$_FILES[$key]['name'][$i].'", "")');
							$db->query('INSERT INTO PhotosIn(url, albumID, caption) VALUES("'.$_FILES[$key]['name'][$i].'", '.$albumID.', "")');
						}
					}
				}
			}
		}
	
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>Big Red Bears</title>
		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="js/addphotos.js"></script>
		<link href='http://fonts.googleapis.com/css?family=Ubuntu+Condensed' rel='stylesheet' type='text/css' />
        <link type="text/css" rel="stylesheet" href="css/mainStyle.css"/>
		<link type="text/css" rel="stylesheet" href="css/addphotoscomplete.css"/>
        
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
				<h1 id="pageTitle">Add Photos</h1>
			</div>
			<div class="divide"></div>

			<div id="main">
				<div id="contentPanel">
					<h2 id="centerTitle">Upload Results</h2>
					
					<!-- Titles Div -->
					<div id="titles">
						<p id="leftTitle">Files</p>
						<p id="rightTitle">Status</p>
					</div>
					
					<!-- Results List -->
					<div id="resultsList">
					<?php
						$failedCount=0;
						$succCount=0;
						//Printing the results of the upload
						foreach($results as $key => $value){
							if($key!=''){
								echo '
									<div class="uploads">
										<p class="filename">'.$key.'</p>';
										
								if($value=='good'){
									$succCount++;
									echo '<img class="statusSucc" src="siteImages/successful.png" alt="Successful Uploads" />';
								}else{
									$failedCount++;
									echo '<p class="statusFail">'.$value.'</p>';
								}
								echo '</div>';
							}
						}
					
					?>
					</div>
					
					<!-- Statistic Summary Panel -->
					<div id="listSummary">
						<img id="icon1" src="siteImages/files.png" alt="Total Files" /> 
						<p id="total">
						<?php
							echo $succCount+$failedCount;
						?>
						</p>
						<img id="icon2" src="siteImages/successful.png" alt="Successful Uploads"/>
						<p id="totalSuccess">
						<?php
							echo $succCount;
						?>
						</p>
						<img id="icon3" src="siteImages/failed.png" alt="Failed Uploads" />
						<p id="totalFail">
						<?php
							echo $failedCount;
						?>
						</p>
					</div>
					
					<!-- Navigation Links -->
					<div id="navLinks">
						<a id="addMore" href="addPhotos.php" title="Add Photos">Add More</a>
						<?php
							echo'<a id="viewAlbum" href="viewPhotos.php?albumID='.$albumID.'" title="View Album">View Album</a>';
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

<?php	
		}
	}
?>

