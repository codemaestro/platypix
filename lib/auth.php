<?php
// get environment variables
$env = parse_ini_file('../.env');
define('ACCESSLOG', $env['ACCESSLOG']);
define('ACCESSPASS', $env['ACCESSPASS']);
define('COOKIESET', $env['COOKIESET']);
define('COOKIEVAL', $env['COOKIEVAL']);

// define logging function
function log_msg($message)
{
  file_put_contents(ACCESSLOG, $message . "\n", FILE_APPEND);
}

// check if the user is logged in
// allow no password setting
session_start();
if (
  trim(ACCESSPASS) === '' ||
  isset($_SERVER["DOCUMENT_ROOT"]) && empty($_SERVER["DOCUMENT_ROOT"])) {
  $_COOKIE[COOKIESET] = COOKIEVAL; // Simulate a local environment for testing
  $_SESSION['logged_in'] = true;
}

if (
  !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true ||
  !isset($_COOKIE[COOKIESET]) || $_COOKIE[COOKIESET] !== COOKIEVAL
) {
  // Determine the current URL (except for /login.php)
  $currentUrl = $_SERVER['REQUEST_URI'];
  if ($currentUrl === '/login.php') {
    $redirectUrl = '/';
  } else {
    $redirectUrl = $currentUrl;
  }

  // If form submitted, check password
  if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['password']) && $_POST['password'] === ACCESSPASS) {
      setcookie(COOKIESET, COOKIEVAL, time() + 60 * 60 * 24 * 7, '/');
      $_SESSION['logged_in'] = true;
      // Redirect to the original page if REDIR param exists, else home
      $redir = '/';
      if (isset($_GET['REDIR'])) {
        $candidate = $_GET['REDIR'];
        // Fix server-side unvalidated URL redirection error by
        // only allowing relative paths starting with /
        if (strpos($candidate, '/') === 0 && strpos($candidate, '//') === false) {
          $redir = $candidate;
        }
      }
      header("Location: $redir");
      exit;
    } else {
      $error = "Invalid password.";
    }
  } else {
    session_destroy();
    if ($_SERVER['PHP_SELF'] !== '/login.php') {
      // Redirect to login page if not already there
      header('Location: /login.php?REDIR=' . urlencode($redirectUrl));
      exit;
    }
  }
}

// Log the access
$date = date('m/d/Y h:i:s a', time());
$request = sprintf(
  "%s%s",
  isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'DOMAIN.COM',
  isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'REQUEST_URI'
);
log_msg("{$request} accessed at {$date}");
