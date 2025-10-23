
<?php require './backend/loginhandler.php' ?>

<?php require './includes/header.inc.php' ?>

<div class="login-container">
    <div class="login-card">
        <img src="./images/logos/asora_logo_black.png" alt="Logo" style="width: 5rem; margin-bottom: 1em;">
        <h1 style="font-weight: 500; font-size: 18px">Welcome back!</h1>

        <?php if (!empty($message)): ?>
          <p style="color: red;"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form 
          action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" 
          method="POST" 
          style="display: flex; flex-direction: column; gap: 1em; margin-top: 1em;"
        >
            <input type="text" class="input-login" placeholder="Username" name="username" required>
            <input type="password" class="input-login" placeholder="Password" name="password" required>
            <button type="submit" class="button-login">Sign In</button>
        </form>
    </div>
</div>

<?php include './includes/footer.inc.php'; ?>
</body>
</html>