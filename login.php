<?php
/**
 * login.php
 * Login page. Asks for a login name (e-mail) and a password, checks them
 * against the database (attempt_login) and, if they are correct, opens
 * the user's session and redirects to the home page.
 */
require_once __DIR__ . '/includes/auth.php';

// Already logged in: no need to show the login form again
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';

// The form was submitted: validate the input and try to log in
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginname = trim($_POST['loginname'] ?? '');
    $password  = (string) ($_POST['password'] ?? '');

    if ($loginname === '' || $password === '') {
        $error = 'Παρακαλώ συμπληρώστε όνομα χρήστη και κωδικό πρόσβασης.';
    } else {
        $user = attempt_login($loginname, $password);
        // Correct credentials: start the session and go to the home page
        if ($user) {
            start_session_for($user);
            header('Location: index.php');
            exit;
        }
        $error = 'Λανθασμένο όνομα χρήστη ή κωδικός πρόσβασης.';
    }
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <title>MathLab - Πιστοποίηση</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-body">
  <div id="page">
    <div id="header">
      <p class="brand">MathLab</p>
      <h1 class="page-title">Πιστοποίηση</h1>
    </div>

    <div id="body-wrapper" class="login-wrapper">
      <div id="content">
        <div class="card login-card">
          <?php if ($error !== ''): ?>
            <p class="error-message"><?= h($error) ?></p>
          <?php endif; ?>

          <form class="contact-form" action="login.php" method="post">
            <label for="loginname">Login</label>
            <input type="text" id="loginname" name="loginname" placeholder="e-mail" required autofocus
                   value="<?= h($_POST['loginname'] ?? '') ?>">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Είσοδος</button>
          </form>
        </div>
      </div>
    </div>

    <div id="footer">&copy; 2026 MathLab</div>
  </div>
</body>
</html>
