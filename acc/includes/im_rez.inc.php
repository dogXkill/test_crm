<?
//-------------------------------------------------------------------------------
//Spath1-путь исходной картинки, $patch2-путь сохранения уменьшеной картинки
//-------------------------------------------------------------------------------
function ImResize($patch, $patch2, $w=200, $h=100, $ql=95, $flx=0, $fly=0,
$cr=1, $alx=1, $aly=1) //$x,$y -ширина и высота уменьшенной картинки
	{	
	global $error;
	
	if(($flx==0)&&($fly==0)) $flx=$fly=1;
	$img_prop = getimagesize($patch);	
	$ex = '';
	
	switch($img_prop['mime']) {
		case 'image/gif':
			$im = @imagecreatefromgif($patch);
			$ex = '.gif';
			break;
		case 'image/jpeg':
			$im = @imagecreatefromjpeg($patch);
			$ex = '.jpeg';
			break;
		case 'image/png':	
			$im = @imagecreatefrompng($patch);
			$ex = '.png';
			break;
		default:
			$error = 'Поддерживаются кртинки только форматов "gif, jpeg, png"';
			return false;
	}

	if(($w==0)&&($h==0)) {
		imagejpeg ($im,$patch2,$ql);
		imagedestroy($im);
		return; 
	}
	$sx=$sy=$bx=$by=0;
	$bww=imagesx($im);
	$bhh=imagesy($im); 		//размеры исходной картинки
	$bw=$bww;
	$bh=$bhh;
	$mny=$bhh/$h;
	if($mny<1) 
		$mny=1;		//вычисление множителя
	$mnx=$bww/$w;
	if($mnx<1) 
		$mnx=1;
	$mnsxy=$w/$h;
	$mnbxy=$bww/$bhh;
	$swy=$bw/$mny;
	$shy=$bh/$mny;
	$swx=$bw/$mnx;
	$shx=$bh/$mnx;
	if(($fly==1)&&($flx==0)) {  //по высоте 
		$sw=$swy; $sh=$shy;
		if(($sw>$w)&&($cr==1)) {
			$bw=$w*$mny;
			$sw=$w;				//обрезать ширину
			switch ($alx) {
				case 1: $bx=($bww-$bw)/2; break;
				case 2: $bx=$bww-$bw; 
			}
		}
	}
	if(($flx==1)&&($fly==0))  { //по ширине 
		$sw=$swx; $sh=$shx; 
		if(($sh>$h)&&($cr==1)) { 
			$bh=$h*$mnx;
			$sh=$h;//обрезать высоту
			switch ($aly) {
				case 1: $by=($bhh-$bh)/2; break;
				case 2: $by=$bhh-$bh; 
			}
		}
	}
	if(($flx==1)&&($fly==1)&&($cr==0)) {  //по высоте и ширине не обрезая
		if($mnbxy>$mnsxy) {
			$sw=$swx;
			$sh=$shx;
		}
		else {
			$sw=$swy;
			$sh=$shy;
		}	
	}
	if(($flx==1)&&($fly==1)&&($cr==1)) { 	//по высоте и ширине обрезая
		if($mnbxy>$mnsxy) {
			$sw=$swy;
			$sh=$shy;
			$bw=$w*$mny;
			$sw=$w;
			switch ($alx) {
				case 1: $bx=($bww-$bw)/2; break;
				case 2: $bx=$bww-$bw; 
			}   
		}
		else {
			$sw=$swx;
			$sh=$shx; 
			$bh=$h*$mnx;
			$sh=$h;
			switch ($aly) {
				case 1: $by=($bhh-$bh)/2; break;
				case 2: $by=$bhh-$bh; 
			}  
		}	
	}
	$im2=imagecreatetruecolor($sw,$sh);
	imagecopyresampled($im2, $im, $sx,$sy,$bx,$by,$sw,$sh,$bw,$bh);
	imagejpeg ($im2,$patch2,$ql); //сохранение уменьшенной картинки
	imagedestroy($im2);
	imagedestroy($im);
	return $ex;
}
//------------------------------------------------------------------------------------
?>