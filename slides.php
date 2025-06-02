<?php
// read file list from images folder
$images = glob('images/*.{jpg,jpeg,png,gif}', GLOB_BRACE);

$slides = [];
foreach ($images as $image) {
  $slides[] = [
    'src' => $image,
    'caption' => pathinfo($image, PATHINFO_FILENAME)
  ];
}
// Encode the slides array as JSON
$slidesJson = json_encode($slides);
// Set the content type to application/json
header('Content-Type: application/json');
// Output the JSON
echo $slidesJson;
?>