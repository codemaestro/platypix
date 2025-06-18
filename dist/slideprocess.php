<?php
// run only from local machine running Five Server
if (isset($_SERVER["DOCUMENT_ROOT"]) && ! empty($_SERVER["DOCUMENT_ROOT"])) {
  echo "This script can only be run locally.";
  exit;
} else {
  echo "Five Server environment detected. Running script...";
  flush();

  // Function to correct image orientation based on EXIF data
  function correctImageOrientation($filename, $image)
  {
    if (function_exists('exif_read_data')) {
      $exif = @exif_read_data($filename);
      if (!empty($exif['Orientation'])) {
        switch ($exif['Orientation']) {
          case 3:
            $image = imagerotate($image, 180, 0);
            break;
          case 6:
            $image = imagerotate($image, -90, 0);
            break;
          case 8:
            $image = imagerotate($image, 90, 0);
            break;
        }
      }
    }
    return $image;
  }

  // Define upload, images, processed, and error directories
  $sourceDir = './uploads/original/';
  $processedDir = './uploads/res-original/';
  $galleryDir = './uploads/res-gallery/';
  $imagesDir = './images/';
  $compSlide = 80; // Compression quality for slide images
  $compGallery = 30; // Compression quality for gallery images
  $errorDir = './uploads/error/';

  // Ensure directories exist
  if (!is_dir($processedDir)) mkdir($processedDir, 0755, true);
  if (!is_dir($galleryDir)) mkdir($galleryDir, 0755, true);
  if (!is_dir($errorDir)) mkdir($errorDir, 0755, true);

  // read list of files in source directory
  $files = array_diff(scandir($sourceDir), array('.', '..'));

  foreach ($files as $file) {
    if (substr($file, 0, 1) === '.') {
      continue; // Skip hidden files
    }
    $filePath = $sourceDir . $file;
    if (is_file($filePath)) { // Skip hidden files
      $fileType = strtolower(pathinfo($file, PATHINFO_EXTENSION));
      $caption = pathinfo($file, PATHINFO_FILENAME);

      // Validate file type
      $allowedTypes = ['bmp', 'jpg', 'jpeg', 'png', 'gif', 'heic'];
      if (in_array($fileType, $allowedTypes)) {
        // Load the image
        switch ($fileType) {
          case 'bmp':
            $image = imagecreatefrombmp($filePath);
            break;
          case 'jpg':
          case 'jpeg':
            $image = imagecreatefromjpeg($filePath);
            break;
          case 'png':
            $image = imagecreatefrompng($filePath);
            break;
          case 'gif':
            $image = imagecreatefromgif($filePath);
            break;
          case 'heic':
            if (extension_loaded('imagick')) {
              $imagick = new Imagick($targetFile);
              $imagick->setImageFormat('jpeg');
              $imagick->writeImage($newFilePath);
              $imagick->clear();
              $imagick->destroy();
            } else {
              throw new Exception('HEIC file could not be processed.');
            }
            break;
          default:
            throw new Exception('Unsupported file type: ' . $fileType);
            break; // Skip unsupported types
        }

        // Correct orientation if necessary
        $image = correctImageOrientation($filePath, $image);

        // Save the slideshow version in the images directory
        $processedFilePath = $imagesDir . $caption . '.jpg';
        imagejpeg($image, $processedFilePath, $compSlide);

        // Save a lower resolution version for gallery
        $galleryFilePath = $galleryDir . $caption . '.jpg';
        imagejpeg($image, $galleryFilePath, $compGallery);

        // Free up memory
        imagedestroy($image);

        // Move original file to full res files directory
        rename($filePath, $processedDir . basename($file));
      } else {
        // Move unsupported file to error directory
        rename($filePath, $errorDir . basename($file));
      }
    }
  }
  echo "Slide processing completed successfully.";
}
