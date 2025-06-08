<?php
include_once '../lib/auth.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login</title>
  <link rel="stylesheet" href="slides.css<?php echo '?v=' . filemtime('slides.css'); ?>" />
</head>

<body>
  <div class="login-container">
    <h1>Login</h1>
    <?php if (isset($error)): ?>
      <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post" action="">
      <p><label for="password">Password:</label>
        <input type="password" id="password" name="password" required /><br>
        <a href="#" id="toggle-password">Show password</a>
      </p>
      <button type="submit">Login</button>
    </form>
  </div>
  <script>
    // Focus the password input field when the page loads
    document.getElementById('password').focus();
    // Toggle password visibility
    document.getElementById('toggle-password').addEventListener('click', function(e) {
      e.preventDefault();
      const pwd = document.getElementById('password');
      if (pwd.type === 'password') {
        this.classList.add('active');
        pwd.type = 'text';
        this.textContent = 'Hide password';
      } else {
        this.classList.remove('active');
        pwd.type = 'password';
        this.textContent = 'Show password';
      }
    });
  </script>
  <?php include_once '../lib/footer.php'; ?>
</body>

</html>