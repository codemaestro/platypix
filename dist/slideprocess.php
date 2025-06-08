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
  $imagesDir = './images/';
  $sourceDir = './uploads/original/';
  $processedDir = './uploads/saved/';
  $errorDir = './uploads/error/';

  // read list of files in source directory
  $files = array_diff(scandir($sourceDir), array('.', '..'));

  foreach ($files as $file) {
    echo "Processing file: $file\n";
    flush();
    $filePath = $sourceDir . $file;
    if (is_file($filePath)) {
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
              throw new Exception('Imagick extension not available for HEIC processing.');
            }
            break;
          default:
            throw new Exception('Unsupported file type: ' . $fileType);
            break; // Skip unsupported types
        }

        // Correct orientation if necessary
        $image = correctImageOrientation($filePath, $image);

        // Save the processed image as JPG
        $processedFilePath = $imagesDir . $caption . '.jpg';
        imagejpeg($image, $processedFilePath, 80); // 90% quality

        // Free up memory
        imagedestroy($image);

        // Move original file to processed files directory
        rename($filePath, $processedDir . basename($file));
      } else {
        // Move unsupported file to error directory
        rename($filePath, $errorDir . basename($file));
      }
    }
  }
  echo "Slide processing completed successfully.";
}
