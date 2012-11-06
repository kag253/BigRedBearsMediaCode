window.onload=initialize;

//Used to keep track of the list colors
var useColorOne=false;

//Keeps track of how many files are in the list
var listCounter=0;

//Used to create new names
var counter=0;

/* Initializes the page */
function initialize(){
	
	//Hides the extra input fields if an album is selected
	$('select[name=albumSelect]').change(function(){
		var currVal=$('select[name=albumSelect]').val();
		if(currVal!="Create A New Album"){
			$('label').css('display', 'none');
			$('#albumTitle').css('display', 'none');
			$('#albumDes').css('display', 'none');
			makeSmall();
		}else{
			makeBig();
			fadeIn();
		}
	});
	
	//Adds uploads to the list 
	$('#photoUpload').live('change', addUpload);
	
	//Clears the list when the clear button is clicked
	$('#clearButton').live('click', clearList);
	
	//Clears a specific file
	$('.uploads img').live('click', removeFile);
	
}

/* Adds uploads to the list in the upload panel*/
function addUpload(){

	var files=$('#photoUpload')[0].files;
	
	//Adjusts for whether or not multiple uploads are allowed or not
	var fileArray=new Array();
	if(files){
		for(var i=0; i<files.length; i++){
			fileArray[i]=files[i].name;
		}
	}else{
		fileArray[0]=$('#photoUpload').val();
	}
	
	//Processing the upload(s)
	for(var i=0; i<fileArray.length; i++){
	
		//Retrieving the filename and the filename with the period
		//replaced by an underscore
		var filename=fileArray[i];
		var lastIndex=filename.lastIndexOf('\\');
		if(lastIndex!=-1){
			filename=filename.substring(lastIndex+1);
		}
		var uFilename= filename.replace('.', '_');
		uFilename= uFilename.replace(' ', '_');
		
		//Inserts if the file is not already in the list or was previously removed
		if($('input[name="'+uFilename+'"]').length <= 0 || $('input[name="'+uFilename+'"]').val()=='false'){
			
			//Adding to the file list
			if(useColorOne){
				var classColor="color1";
				useColorOne=false;
			}else{
				var classColor="color2";
				useColorOne=true;
			}
			$('#uploadsSoFar').prepend('<div class="uploads '+classColor+'"><p>'+filename+
			'</p><img src="siteImages/tinyx.png" alt="Delete" /></div>');
	
			//Updating the list counter
			listCounter+=1;
			$('#listCounter').text(listCounter+' Files');
			
			//Setting the file status marker 
			if($('input[name="'+uFilename+'"]').length <= 0 ){
				$('#uploadsSoFar').prepend('<input type="hidden" name="'+uFilename+'" value="true" />');
			}else{
				$('input[name="'+uFilename+'"]').val('true');
			}
		}
	}


	//Hides the input
	$('#photoUpload').attr('name', 'photoUpload'+counter+'[]');
	$('#photoUpload').attr('class', 'hiddenInputs');
	$('#photoUpload').attr('id', 'photoUpload'+counter);
	counter++;
	
	//Inserting a new input
	$('<input id="photoUpload" type="file" name="photoUpload[]" multiple />').insertBefore($('#uploadsSoFar'));
	
	
}

/* Clears the list of uploads when the clear button is pressed*/
function clearList(){
	$('div.hiddenInputs').empty();
	$('#uploadsSoFar').empty();
	$('#listCounter').text('0 Files');
	listCounter=0;
	useColorOne=false;
}

/* Removes a specific file from the list*/
function removeFile(){
	//Removing the file
	var parentNode=$(this).parent();
	var filename=parentNode.children(':first').text();
	var uFilename= filename.replace('.', '_');
	$('input[name="'+uFilename+'"]').val('false');
	($(this).parent()).remove();
	
	//Decrementing file counter
	listCounter=listCounter-1;
	$('#listCounter').text(listCounter+' Files');
	
	//Re-adjusting alternating color scheme
	useColorOne=false;
	$('.uploads').each(function(){
		if(useColorOne){
			$(this).attr('class', 'uploads color1');
			useColorOne=false;
		}else{
			$(this).attr('class', 'uploads color2');
			useColorOne=true;
		}
	});
	
	
}

/* Gradually decreases the height of the albumSelect div */
function makeSmall(){
	for(i=1; i<=200; i++){
		setTimeout(function(){
			var tempString=$('#albumSelect').css('height');
			var index=tempString.indexOf('p');
			var currHeight=parseInt(tempString.substring(0, index));
			if(currHeight > 100){
				$('#albumSelect').css('height', ''+(currHeight-1)+'px');
			}
		}, i)
	}
}

/* Gradually increases the height of the albumSelect div */
function makeBig(){
	for(i=1; i<=200; i++){
		setTimeout(function(){
			var tempString=$('#albumSelect').css('height');
			var index= tempString.indexOf('p');
			var currHeight=parseInt(tempString.substring(0, index));
			if(currHeight < 300){
				$('#albumSelect').css('height', ''+(currHeight+1)+'px');
			}
		}, i)
	}
}

/* Gradually fades in the textboxes in the album panel */
function fadeIn(){
	//Setting the opacity to 0
	$('label').css('opacity', '0.0');
	$('#albumTitle').css('opacity', '0.0');
	$('#albumDes').css('opacity', '0.0');
	
	//Displaying them as blocks
	$('label').css('display', 'block');
	$('#albumTitle').css('display', 'block')
	$('#albumDes').css('display', 'block')
	
	for(i=1; i<=100; i++){
		setTimeout(function(){
			var currOpacity=parseFloat($('label').css('opacity'));
			if(currOpacity<1.0){
				$('label').css('opacity', ''+(currOpacity+0.01));
			}
			currOpacity=parseFloat($('#albumTitle').css('opacity'));
			if(currOpacity<1.0){
				$('#albumTitle').css('opacity', ''+(currOpacity+0.01));
			}
			currOpacity=parseFloat($('#albumDes').css('opacity'));
			if(currOpacity < 1.0){
				$('#albumDes').css('opacity', ''+(currOpacity+0.01));
			}
		}, i*10)
	}
}

