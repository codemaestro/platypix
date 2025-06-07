<?php
// get environment variables
$env = parse_ini_file('../.env');
define('LOGFILE', $env['LOGFILE']);
define('SECRET', $env['SECRET']);

// define logging function
function log_msg($message)
{
  file_put_contents(LOGFILE, $message . "\n", FILE_APPEND);
}

// push another change to trigger the deploy
$post_data = file_get_contents('php://input');
$signature = hash_hmac('sha1', $post_data, SECRET);

// This should break if the signature is not set or does not match
if (
  !isset($_SERVER['HTTP_X_HUB_SIGNATURE']) ||
  empty($_SERVER['HTTP_X_HUB_SIGNATURE']) ||
  !hash_equals('sha1=' . $signature, $_SERVER['HTTP_X_HUB_SIGNATURE'])
) {
  $error = "Invalid signature received.";
}

// Log the trigger before doing it
$date = date('m/d/Y h:i:s a', time());
log_msg("Webhook triggered deploy at {$date}");

// crash if the deploy does not have a signature
if (empty($error)) {
  // Run deploy command and log output
  $output_lines = [];
  exec('git pull', $output_lines);
  if (!empty($output_lines)) {
    log_msg(implode("\n", $output_lines));
  }
} else {
  log_msg("Error: {$error}");
}

log_msg("Deployment completed at {$date}

---
");
header('Location: /');
