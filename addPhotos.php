<?php
	@session_start();
	
	//Checking if the user should be allowed on this page
	if(!isset($_SESSION['user'])){
		require('index.php');
	}else{
		//Setting up the database connection
		require('db_info.inc');
		$db=new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
		
		ini_set('memory_limit', '128M');
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
		<link type="text/css" rel="stylesheet" href="css/addPhotos.css"/>
        
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
					<form action="addphotoscomplete.php" method="post" enctype="multipart/form-data">
						<div id="uploadDiv">
							
							<!-- Album Panel -->
							<div id="albumSelect">
								<h2>Choose an Album</h2>
								<select name="albumSelect">
									<option>Create A New Album</option>
									<?php
										//Querying the database for all the albums created
										$query='SELECT albumID, albumTitle FROM Albums';
										$titles=$db->query($query);
										
										//Displaying all album titles
										while($array=$titles->fetch()){
											echo '<option>'.$array[0].' - '.$array[1].'</option>';
										}
										
										$db=null;
									?>
								</select>
								<label for="albumTitle">Album Title</label>
								<input id="albumTitle" name="albumTitle" type="text" />
								<label for="albumDes">Album Description</label>
								<textarea id="albumDes" name="albumDes"></textarea>
							</div>
							
							<!-- Photo Panel -->
							<div id="photoInput">
								<h2>Upload Photos</h2>
								<input id="photoUpload" type="file" name="photoUpload[]" multiple />
								<p id="maxSizeWarning">Total maximum upload size is 128MB</p>
								<div id="uploadsSoFar"></div>

								<p id="listCounter">0 Files</p>
								<input id="clearButton" name="clearButton" type="button" value="Clear All" />
						
							</div>
							<!-- Error Message Panel -->
							<?php
								if(isset($_SESSION['photoCompleteError'])){
									echo '
										<div id="errorBox">
											<img id="errorIcon" src="siteImages/error.png" alt="Error" />
											<p id="errorMsg">'.$_SESSION['photoCompleteError'].'</p>
										</div>
									';
									unset($_SESSION['photoCompleteError']);
								}
							?>
							
							<input id="submitButton" name="submitButton" type="submit" value="Upload Photos" />
						</div>
						
					</form>
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