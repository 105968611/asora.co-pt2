<?php
session_start();
include_once './database/settings.php';

//Log out
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Clear session
    $_SESSION = [];
    session_destroy();

    // Redirect to login page
    header("Location: login.php");
    exit();
}

$message = '';
if (isset($_GET['error'])) {
  switch ($_GET['error']) {
    case 'empty':
      $message = 'Please fill in both username and password.';
      break;
    case 'invalid':
      $message = 'Incorrect username or password.';
      break;
    case 'server':
      $message = 'Something went wrong. Please try again later.';
      break;
  }
}


?>

<?php  $page_title = "Login - Asora"; 
require './includes/header.inc.php' ?>

<body class=body>

  <?php require('./includes/navbar.inc.php'); ?>

  <div class="login-container">
    <div class="login-card">
      <img src="./images/logos/asora_logo_black.png" alt="asora-logo" style="width: 5rem; margin-bottom: 1em;">
      <h1 style="font-weight: 500; font-size: 18px">Welcome back!</h1>

      <?php if (!empty($message)): ?>
        <p style="color:red;"><?php echo htmlspecialchars($message); ?></p>
      <?php endif; ?>

      <form
        action="./backend/loginhandler.php"
        method="POST"
        style="display: flex; flex-direction: column; gap: 1em; margin-top: 1em;">
        <label for="username" class="sr-only">Username</label>
        <input id="username" type="text" class="input-login" placeholder="Username" name="username" required>
        <label for="password" class="sr-only">Password</label>
        <input id="password" type="password" class="input-login" placeholder="Password" name="password" required>
        <button type="submit" class="button-login">Sign In</button>
      </form>
    </div>
  </div>

  <?php include './includes/footer.inc.php'; ?>
</body>

</html>