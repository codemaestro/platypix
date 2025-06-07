<?php
include_once('../lib/auth.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Steve's 80th birthday web slideshow app</title>

  <link rel="stylesheet" href="slides.css<?php echo '?v=' . filemtime('slides.css'); ?>" />
  <script src="slides.js<?php echo '?v=' . filemtime('slides.css'); ?>" async defer></script>
</head>

<body>
  <div id="container">
    <div id="slide">
      <div>
        <img id="slideshow-img" class="fade show" src="" />
      </div>
    </div>
  </div>
  <?php
  include_once '../lib/footer.php';
  ?>
</body>

</html>