window.onload=initialize;

// global request variable
var request;
var maxPhotoHeight = 630;
var maxPhotoWidth = 840;
var imageElement;
var imageCount;
var next;



/* Intializes the functionality on the page */
function initialize(){
	imageCount = $('.thumbs img').length;
	
	// Attaching the click event handlers for the modal window that pops up when 
	// a user clicks on a thumbnail
	$('.thumbs').live('click', displayInfo);
	$('#closePicture').live('click', hideInfo);
	$('#closePicture').live('mouseover', function(){
		$('#closePicture').css('display', 'block');
	});
	$('#fadeOut').live('click', hideInfo);
	$('#nextPic').live('click', function(){
		switchPhoto(true);
	});
	$('#prevPic').live('click', function(){
		switchPhoto(false);
	});
	
	// Attaching the click event handlers for the buttons next to the album description.
	// These buttons are only avaialable to members of the club.
	$('#editAlbum').live('click', function(){
		$('#editAlbum, #deleteAllPhotos, #deleteAlbum').css('display', 'none');
		$('#albumDesc').css('display', 'none');
		$('#descTextArea, #makeAlbumChanges, #cancelChanges').css('display', 'block');
		$('#descTextArea').text($('#albumDesc').text());
	});
	$('#cancelChanges').live('click', function(){
		$('#editAlbum, #deleteAllPhotos, #deleteAlbum').css('display', 'block');
		$('#albumDesc').css('display', 'block');
		$('#descTextArea, #makeAlbumChanges, #cancelChanges').css('display', 'none');
	});
	$('#makeAlbumChanges').live('click', updateAlbum);
	$('#deleteAllPhotos').live('click', deleteAllPhotos);
	
	
	//This shows a pop-up confirmation box for the 'delete album' button
	$('#albumEditButtons').submit(function(){
		return confirm('Are you sure you want to delete this album?');
	});
	
	//Setting the initial value of the day select
	$('#month').live('change', function(){
		var dayNum= dayCount(parseInt($('#month').val()));
		var currMaxDay= parseInt($('#maxDay').val());
		if(currMaxDay>dayNum){
			var diff=currMaxDay-dayNum;
			for(i=1; i<=diff; i++){
				$('#day option').last().remove();
			}
			$('#day option').last().attr('id', 'maxDay');
		}else if(dayNum>currMaxDay){
			var diff=dayNum-currMaxDay;
			for(i=currMaxDay+1; i<dayNum; i++){
				$('#day').append('<option>'+i+'</option>');
			}
			$('#maxDay').attr('id', '');
			$('#day').append('<option id="maxDay">'+dayNum+'</option>');
		}
	});
	
	
	

	// Setting up request object
	request = null;
	createRequest();

}

/* Makes an AJAX call to delete all the photos in the album */
function deleteAllPhotos(){
	
	var confirmation=confirm('Are you sure you want to delete all the photos in this album?');
	if(confirmation){
		// Retrieving the data needed to make the AJAX call
		var name=$('#makeAlbumChanges').attr('name');
		var albumID=name.substring(16);
		alert('the album id is: '+albumID);
		// Making the AJAX call
		var url='removeAllPhotos.php';
		var msg='albumID='+albumID;
		request.open("POST", url, true);
		request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		request.setRequestHeader("Content-length", 	msg.length);
		request.setRequestHeader("Connection", "close");
		request.onreadystatechange = confirmDeleteAll;
		request.send(msg);
	}


}

/* Processes the results from the AJAX call to delete all photos*/
function confirmDeleteAll(){

	// Returns if the request is not completely done
	if(request.readyState < 4){
		return;
	}
	
	// Removing the photos from the page and hiding the button to delete photos
	$('.photoRow').remove();
	$('#deleteAllPhotos').css('display', 'none');
	
	// Displaying the "No Photos" text and image
	$('<div id="zeroPhotos"><img src="siteImages/photoStack.png" alt="Photo Icon" />'+
	'<h2>There are no photos in this album.</h2>'+
	'<a href="addPhotos.php">Add Photos</a></div>').insertBefore('#editButtons');
}

/*  Makes an AJAX call to update the album description */
function updateAlbum(){
	// Altering the albumInfo panel
	$('#makeAlbumChanges, #descTextArea, #cancelChanges').css('display', 'none');
	$('#albumDesc, #editAlbum, #deleteAllPhotos, #deleteAlbum').css('display', 'block');

	// Retrieving the data needed to make the AJAX call
	var name=$('#makeAlbumChanges').attr('name');
	var albumID=name.substring(16);
	var description=$('#descTextArea').val();
	
	
	// Making the AJAX call
	var url='updateAlbumInfo.php';
	var msg='albumID='+albumID+'&description='+description;
	request.open("POST", url, true);
	request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	request.setRequestHeader("Content-length", 	msg.length);
	request.setRequestHeader("Connection", "close");
	request.onreadystatechange = confirmUpdate;
	request.send(msg);
}

/* 
*  Checks to see if the album update succeeded and changes the album
*  description if it did
*/
function confirmUpdate(){

	// Returns if the request is not completely done
	if(request.readyState < 4){
		return;
	}
	
	// Processing the results
	var result=$.parseJSON(request.responseText);
	var success=result.success;
	if(success){
		$('#albumDesc').text(result.description);
	}


}



/* Displays a larger version of the clicked image and its details */
function displayInfo(){

	// Retrieving the filename 
	var filename='siteImages/bigDefault.png';
	imageElement = $(this).children();
	var filename = imageElement.attr('src');
	var lastIndex=filename.lastIndexOf('/');
	if(lastIndex!=-1){
		filename=filename.substring(lastIndex+1);
	}
	
	// Displaying some animation and the large photo
	$('#fadeOut, #imagePopUp').css('display', 'block');
	$('#imagePopUp').animate({
		height: '630px', 
		width: '840px'
	}, 500);
	$('#enlargedPhoto, #closePicture').css('display', 'none');
	$('#enlargedPhoto').attr('src', 'photos/fullSize/'+filename);
	$('#enlargedPhoto').load(function(){
		$('#loadingMsg').css('display', 'none');
		var topValue = (maxPhotoHeight-$('#enlargedPhoto').height())/2;
		var leftValue = (maxPhotoWidth-$('#enlargedPhoto').width())/2;
		$('#enlargedPhoto').css('top', topValue+'px');
		$('#enlargedPhoto').css('left', leftValue+'px');
		$('#enlargedPhoto').css('display', 'block');
	});
	setTimeout(function(){
			$('#enlargedPhoto').animate({
				opacity: '1.0'
			}, 250);
		}, 500);
	
	// Attaching event handlers to the enlarged photo and its features 
	$('#imagePopUp').live('hover', function(){
		$('#closePicture').css('display', 'block');
		$('#closePicture, #prevPic, #nextPic').css('display', 'block');
	});
	$('#fadeOut').live('hover', function(){
		$('#closePicture').css('display', 'none');
		$('#closePicture, #prevPic, #nextPic').css('display', 'none');
	});

	
	
	//alert('The height of the panel is '+$('#imagePopUp').css('height'));
	
	//var testT = document.getElementById('imagePopUp');
	//var testH = testT.height;
	//alert('The test is '+testH);
	
	
	// Making Ajax call to retieve the date taken and event description
	/*lastIndex=filename.lastIndexOf('.');
	var shortFilename='';
	var extension='jpg';
	if(lastIndex!=-1){
		shortFilename=filename.substring(0,lastIndex);
		extension=filename.substring(lastIndex+1);
	}
	
	var url='retrievePhotoInfo.php?photoUrl='+shortFilename+'&fileType='+extension;
	$('#imageDetails').text('Retrieving photo information, please wait.');
	request.open("GET", url, true);
	request.onreadystatechange = processInfo;
	request.send(null);*/

}

/* Switches to the next or previous photo in the album */
function switchPhoto(next){

	// Finding the next or previous photo
	var idName = imageElement.attr('id');
	var number = idName.substring(5);
	if(next){
		number++;
		if(number > imageCount){
			imageElement = $('#photo1');
		}else{
			imageElement = $('#photo'+number);
		}
		
	}else{
		number--;
		if(number <= 0){
			imageElement = $('#photo'+imageCount);
		}else{
			imageElement = $('#photo'+number);
		}
	}

	// Finding the filename of the next or previous photo
	var filename='siteImages/bigDefault.png';
	var filename = imageElement.attr('src');
	var lastIndex=filename.lastIndexOf('/');
	if(lastIndex!=-1){
		filename=filename.substring(lastIndex+1);
	}
	
	// displaying the next or previous photo
	$('#enlargedPhoto').attr('src', 'photos/fullSize/'+filename);
	$('#enlargedPhoto').load(function(){
		$('#loadingMsg').css('display', 'none');
		var topValue = (maxPhotoHeight-$('#enlargedPhoto').height())/2;
		var leftValue = (maxPhotoWidth-$('#enlargedPhoto').width())/2;
		$('#enlargedPhoto').css('top', topValue+'px');
		$('#enlargedPhoto').css('left', leftValue+'px');
		$('#enlargedPhoto').css('display', 'block');
	});

	
}


/* Processes the results of the AJAX call for photo information */
function processInfo() {

	//Returns if the request is not completely done
	if(request.readyState < 4){
		return;
	}

	//Processing the results
	var result=$.parseJSON(request.responseText);
	var successStatus=result.success;
	if(successStatus){
		var photoDate=result.date;
		var photoDesc=result.description;
		if((photoDate=='' || photoDate=='0000-00-00') && photoDesc==''){
			$('#imageDetails').text('The date and event description for this photo has not been provided.');
		}else if(photoDate==''){
			$('#imageDate').text('The date for this photo has not been provided.');
			$('#imageDetails').text(photoDesc);
		}else if(photoDesc==''){
			$('#imageDetails').text('The event description for this photo has not been provided.');
			$('#imageDate').text(photoDate);
		}else{
			$('#imageDetails').text(photoDesc);
			$('#imageDate').text(photoDate);
		}
	}else{
		$('#imageDate').text('');
		$('#imageDetails').text('Information retrieval for this photo was unsuccessful.');
	}
	
}


/* Removes the popup panel and faded out background */
function hideInfo(){
	$('#fadeOut, #imagePopUp, #enlargedPhoto, #closePicture').css('display', 'none');
	$('#loadingMsg').css('display', 'block');
}

/* When given the month, returns the correct number of days in that month */
function dayCount(month){
	
	if(month==1 || month==3 || month==5 || month==7 || month==8 || month==10 || month==12){
		return 31;
	}else if(month==2){
		return 29;
	}else{
		return 30;
	}

}


/*
 *
 * This function creates the Ajax request object (Taken from hw1)
 *
 */

function createRequest() {

	// From "Head Rush Ajax" by Brett McLaughlin

	try {
		request = new XMLHttpRequest();
	} catch (trymicrosoft) {
		try {
			request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (othermicrosoft) {
			try {
				request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (failed) {
				request = null;
			}
		}
	}
	
	if (request == null) {
		alert("Error creating request object!");
	}

}

