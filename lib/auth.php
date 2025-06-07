<?php
// get environment variables
$env = parse_ini_file('../.env');
define('ACCESSLOG', $env['ACCESSLOG']);
define('ACCESSPASS', $env['ACCESSPASS']);

// define logging function
function log_msg($message)
{
  file_put_contents(ACCESSLOG, $message . "\n", FILE_APPEND);
}

// check if the user is logged in
session_start();
if (
  !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true ||
  !isset($_COOKIE['auth']) || $_COOKIE['auth'] !== '1'
) {
  // If form submitted, check password
  if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['password']) && $_POST['password'] === ACCESSPASS) {
      setcookie('auth', '1', time() + 60 * 60 * 24 * 7, '/');
      $_SESSION['logged_in'] = true;
      header('Location: /');
      exit;
    } else {
      $error = "Invalid password.";
    }
  } else {
    session_destroy();
    if ($_SERVER['PHP_SELF'] !== '/login.php') {
      // Redirect to login page if not already there
      header('Location: /login.php');
      exit;
    }
  }
}

// Log the access
$date = date('m/d/Y h:i:s a', time());
$request = sprintf("%s%s",
  isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'DOMAIN.COM',
  isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'REQUEST_URI'
);
log_msg("{$request} accessed at {$date}");
?>