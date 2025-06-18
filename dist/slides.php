<?php
// read file list from images folder
$images = glob('images/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
$skiplogo = false;
if (sizeof($images) > 1) {
  $skiplogo = true;
}

$slides = [];
foreach ($images as $image) {
  $basename = pathinfo($image, PATHINFO_FILENAME);
  if ($skiplogo && strtolower($basename) === 'upload_your_photos') {
    continue; // Skip logo image if more than one image is present
  }
  // Search for the original file with any extension
  $originals = glob("uploads/res-original/{$basename}.*");
  $full = count($originals) > 0 ? $originals[0] : null;

  $slides[] = [
    'src' => $image,
    'caption' => $basename,
    'full' => $full // full path to the original file, or null if not found
  ];
}
// Encode the slides array as JSON
$slidesJson = json_encode($slides);
// Set the content type to application/json
header('Content-Type: application/json');
// Output the JSON
echo $slidesJson;
