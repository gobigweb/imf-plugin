<?php
/**
 * Add frame to picture
 * @param  string $sourcePath Path to picture
 * @param  int    $design     Frame to use
 * @return binary             Binary data of framed picture
 */
function makeDP($sourcePath, $design = 0){
  if(in_array($design, array(0, 1, 2, 3, 4)))
    $design = "frame-$design.png";
  else
    exit;

  $src = imagecreatefromstring(file_get_contents($sourcePath));
  $fg = imagecreatefrompng(__DIR__ ."/public/images/frames/$design");

  list($width, $height) = getimagesize($sourcePath);

  $croppedFG = imagecreatetruecolor($width, $height);

  $background = imagecolorallocate($croppedFG, 0, 0, 0);
  // removing the black from the placeholder
  imagecolortransparent($croppedFG, $background);

  imagealphablending($croppedFG, false);
  imagesavealpha($croppedFG, true);

  imagecopyresized($croppedFG, $fg, 0, 0, 0, 0, $width, $height, 630, 630);

  // Start merging
  $out = imagecreatetruecolor($width, $height);
  imagecopyresampled($out, $src, 0, 0, 0, 0, $width, $height, $width, $height);
  imagecopyresampled($out, $croppedFG, 0, 0, 0, 0, $width, $height, $width, $height);

  ob_start();
  imagepng($out);
  $image = ob_get_clean();

  $outputImage  = imagecreatetruecolor(1200, 630);
  $white = imagecolorallocate($outputImage, 255, 255, 255);
	imagefill($outputImage, 0, 0, $white);

  imagecopyresampled($outputImage, $out, 285, 0, 0, 0,630, 630, 1200, 630);
  ob_start();
  imagepng($outputImage);
  $nimage = ob_get_clean();

  return $nimage;
}

?>