<?php
	
	/*
	*	Takes the image $image from the $src directory, resizes a copy of it to
	*	$height by $width and stores the result in $dest with $marker prepended to
	*	it
	*/
	function resizeImage($image, $src, $dest, $isBig, $maxHeight, $maxWidth){
		ini_set('memory_limit', '128M');
		$imageData=getimagesize($src.$image);
		
		//gif 1, jpeg 2, png 3
		if($imageData[2]==1){
			$img=imagecreatefromgif($src.$image);
		}else if($imageData[2]==2){
			$img=imagecreatefromjpeg($src.$image);
		}else if($imageData[2]==3){
			$img=imagecreatefrompng($src.$image);
		}else{
			return array(false, 'Invalid File Type');
		}
		
		$currWidth=imagesx($img);
		$currHeight=imagesy($img);
		$newHeight=$maxHeight;
		$newWidth=$maxWidth;
		
		//Resizes the image proportionally to be within $maxHeight by $maxWidth
		if($currWidth < $maxWidth && $currHeight < $maxHeight){
			$newHeight=$currHeight;
			$newWidth=$currWidth;
		}else{
			$largerValue=($currHeight>$currWidth ? $currHeight : $currWidth);
			$reduction=$largerValue-$maxHeight;
			$decrease=1.0-(floatval($reduction)/floatval($largerValue));
			$newHeight=$currHeight*$decrease;
			$newWidth=$currWidth*$decrease;
			
		}
		
		$newImage=imagecreatetruecolor($newWidth,$newHeight);
	
		//$xDes=(($newWidth-$width)/2);
		//$yDes=(($newHeight-$height)/2);
		imagecopyresampled($newImage, $img, 0, 0, 0, 0, $newWidth, $newHeight, $currWidth, $currHeight);
		
		//Storing thumbnail
		if($imageData[2]==1){
			@unlink($dest.$image);
			imagegif($newImage, $dest.$image);
		}else if($imageData[2]==2){
			@unlink($dest.$image);
			imagejpeg($newImage, $dest.$image);
		}else{
			@unlink($dest.$image);
			imagepng($newImage, $dest.$image);
		}
		
		//Freeing memory
		imagedestroy($newImage);
		imagedestroy($img);
		ini_set('memory_limit', '16M');
		
		return array(true, '');
		
	}
	
?>