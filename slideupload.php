<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.5">
  <title>Upload images</title>
</head>

<body>
<?php
  // create a web form that receives a file upload with a text field for the caption
  // test that the image is jpg, jpeg, png, gif, or heic
  // save the image to an uploads folder
  // process the image/uploads to save a jpg copy to the images folder
  // name the file using the sanitized text field .jpg
  // move the processed images to the uploads/processed folder
  // move any error images to the uploads/error folder

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

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image']) && isset($_POST['caption'])) {
      // Check if caption is provided and not empty
      // (shouldn't happen due to HTML5 required attribute, but good to check)
      if (empty($_POST['caption'])) {
        echo "Caption cannot be empty.";
        exit;
      }

      // sanitize caption to prevent directory traversal and other issues
      // caption allows alphanumeric characters and some punctuation
      // caption will be used as the filename
      $caption = trim(preg_replace('/[^a-zA-Z0-9_,&\'"‘’“”#%!?\(\)\[\]-]+/', '_', $_POST['caption']));

      // Define upload, images, processed, and error directories
      $imagesDir = './images/';
      $uploadDir = './uploads/';
      $processedDir = './uploads/processed/';
      $errorDir = './uploads/error/';

      // Ensure directories exist
      if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
      if (!is_dir($processedDir)) mkdir($processedDir, 0755, true);
      if (!is_dir($errorDir)) mkdir($errorDir, 0755, true);

      $file = $_FILES['image'];
      $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

      // Validate file type
      $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'heic'];
      if (in_array($fileType, $allowedTypes)) {
        $targetFile = $uploadDir . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
          // Process the image and save as JPG
          $newFileName = $caption . '.jpg';
          $newFilePath = $imagesDir . $newFileName;

          // Convert to JPG using GD or Imagick
          try {
            if ($fileType === 'heic') {
              if (extension_loaded('imagick')) {
                $imagick = new Imagick($targetFile);
                $imagick->setImageFormat('jpeg');
                $imagick->writeImage($newFilePath);
                $imagick->clear();
                $imagick->destroy();
              } else {
                throw new Exception('Imagick extension not available for HEIC processing.');
              }
            } else {
              $image = imagecreatefromstring(file_get_contents($targetFile));
              if (in_array($fileType, ['jpg', 'jpeg'])) {
                $image = correctImageOrientation($targetFile, $image);
              }
              imagejpeg($image, $newFilePath);
              imagedestroy($image);
            }
            // Move original file to processed folder
            rename($targetFile, $processedDir . basename($file['name']));
            echo "Image uploaded and processed successfully: " . htmlspecialchars($newFileName);
          } catch (Exception $e) {
            // Move to error directory on failure
            move_uploaded_file($targetFile, $errorDir . basename($file['name']));
            echo "Error processing image: " . htmlspecialchars($e->getMessage());
          }
        } else {
          echo "Failed to upload file.";
        }
      } else {
        move_uploaded_file($file['tmp_name'], $errorDir . basename($file['name']));
        echo "Invalid file type. Only JPG, JPEG, PNG, GIF, and HEIC are allowed.";
      }
    } else {
      echo "No file or caption provided.";
    }

    echo '<a href="./slideupload.php">Upload another photo</a>';
  } else {
    // Display the upload form
    echo '<form action="" method="post" enctype="multipart/form-data">';
    echo 'Caption: <input type="text" name="caption" required><br>';
    echo 'Select image to upload: <input type="file" name="image" accept=".jpg,.jpeg,.png,.gif,.heic" required><br>';
    echo '<input type="submit" value="Upload Image">';
    echo '</form>';
  }
?>
</body>

</html>
