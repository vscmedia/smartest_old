<?php
# Constants
//define(IMAGE_BASE, '/home/tmp');


if(!defined('SM_ROOT_DIR')){

	chdir('../../../../');
	define("SM_ROOT_DIR", getcwd().DIRECTORY_SEPARATOR);

}

define('IMAGE_BASE', SM_ROOT_DIR.'Public/Resources/Images/');
define('WTMRK_BASE', SM_ROOT_DIR.'Public/Resources/Images/System/');

define('MAX_WIDTH', 80);
define('MAX_HEIGHT', 80);
define('OUTPUT_WIDTH', 80);
define('OUTPUT_HEIGHT', 80);

$image_file = end(explode('/', $_SERVER['REQUEST_URI']));

# Get image location
// $image_file = $conversion_src;
$image_path = IMAGE_BASE . $image_file;
$wtmrk_path = WTMRK_BASE . "generalThumbnail.png";

// echo $wtmrk_path;

//
// This file should be include in all resize operations
// it checks if a cache file exists named <script.php><123232.jp>
//

define('IMAGE_CACHE', SM_ROOT_DIR.'Public/Resources/Images/ImageAssetThumbnails/');

$script = "";
$cachefile = IMAGE_CACHE . $script . $image_file;

// echo $cachefile;

// $image_path = IMAGE_BASE . '/'. $image_file;
// echo $cachefile;
// exit;

if (file_exists($cachefile)) {
  
  $handle = fopen ($cachefile, "r");
  $contents = fread ($handle, filesize ($cachefile));
  fclose ($handle);
  header("Content-type: image/jpeg");
  echo $contents;

  exit();
}


# Load image
$img = null;
$ext = strtolower(end(explode('.', $image_path)));
if ($ext == 'jpg' || $ext == 'jpeg') {
  $img = @imagecreatefromjpeg($image_path);
} else if ($ext == 'png') {
  $img = @imagecreatefrompng($image_path);
# Only if your version of GD includes GIF support
} else if ($ext == 'gif') {
  $img = @imagecreatefromgif($image_path);
}

# If an image was successfully loaded, test the image for size
if ($img) {

    # Get image size and scale ratio
  $width = imagesx($img);
  $height = imagesy($img);
  $scale = min(MAX_WIDTH/$width, MAX_HEIGHT/$height);
  $shortside = min($width, $height);

    # If the image is larger than the max shrink it
  if ($shortside > 0) {

        # Create a new temporary image
    $tmp_img = imagecreatetruecolor(OUTPUT_WIDTH, OUTPUT_HEIGHT);

        # Copy and resize old image into new image
    imagecopyresampled($tmp_img, $img, 0, 0, 0, 0,
		     OUTPUT_WIDTH, OUTPUT_HEIGHT, $shortside, $shortside);
    imagedestroy($img);
    $img = $tmp_img;
    
    // image is now ready for watermarking
    
    $wtmrk_img = @imagecreatefrompng($wtmrk_path);
    imagealphablending($img, TRUE);
    imagesavealpha($img, FALSE);
    imagecopy($img, $wtmrk_img, 0, 0, 0, 0, OUTPUT_WIDTH, OUTPUT_HEIGHT);
  }
}

# Create error image if necessary
if (!$img) {
  $img = imagecreate(MAX_WIDTH, MAX_HEIGHT);
  imagecolorallocate($img,0,0,0);
  $c = imagecolorallocate($img,70,70,70);
  imageline($img,0,0,MAX_WIDTH,MAX_HEIGHT,$c2);
  imageline($img,MAX_WIDTH,0,0,MAX_HEIGHT,$c2);
}

# Display the image
header("Content-type: image/jpeg");
imagejpeg($img);

$fh = fopen($cachefile,"w")
     or die ("Unable to open resized image file for writing");

     ob_start();
     imagejpeg($img);
     $buffer = ob_get_contents();
     ob_end_clean();

     if(!fwrite($fh,$buffer)) {
       print "Cannot write to file ($cachefile)";
       exit();
     }

fclose($fh);
