<?php
/**
 * user_form.php
 * Form for adding a new user or editing an existing one (when ?id= is
 * given). Tutors only. Passwords are always stored hashed
 * (password_hash). When editing, leaving the password field empty keeps
 * the existing password.
 */
require_once __DIR__ . '/includes/auth.php';
require_tutor();

$db = get_db();
$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);

$user = ['first_name' => '', 'last_name' => '', 'loginname' => '', 'role' => 'student'];
$error = '';

if ($id > 0) {
    $stmt = $db->prepare('SELECT first_name, last_name, loginname, role FROM users WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $found = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($found) {
        $user = $found;
    } else {
        $id = 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user['first_name'] = trim($_POST['first_name'] ?? '');
    $user['last_name']  = trim($_POST['last_name'] ?? '');
    $user['loginname']  = trim($_POST['loginname'] ?? '');
    $user['role']       = ($_POST['role'] ?? 'student') === 'tutor' ? 'tutor' : 'student';
    $password           = (string) ($_POST['password'] ?? '');

    if ($user['first_name'] === '' || $user['last_name'] === '' || $user['loginname'] === '') {
        $error = 'Παρακαλώ συμπληρώστε όνομα, επώνυμο και loginname.';
    } elseif (!filter_var($user['loginname'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Το loginname πρέπει να είναι έγκυρη διεύθυνση e-mail.';
    } elseif ($id === 0 && $password === '') {
        $error = 'Παρακαλώ ορίστε κωδικό πρόσβασης για τον νέο χρήστη.';
    } else {
        // Make sure the login name is unique
        $stmt = $db->prepare('SELECT id FROM users WHERE loginname = ? AND id <> ?');
        $stmt->bind_param('si', $user['loginname'], $id);
        $stmt->execute();
        $duplicate = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($duplicate) {
            $error = 'Υπάρχει ήδη χρήστης με αυτό το loginname.';
        } else {
            if ($id > 0) {
                if ($password !== '') {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare('UPDATE users SET first_name=?, last_name=?, loginname=?, role=?, password=? WHERE id=?');
                    $stmt->bind_param('sssssi', $user['first_name'], $user['last_name'], $user['loginname'], $user['role'], $hash, $id);
                } else {
                    $stmt = $db->prepare('UPDATE users SET first_name=?, last_name=?, loginname=?, role=? WHERE id=?');
                    $stmt->bind_param('ssssi', $user['first_name'], $user['last_name'], $user['loginname'], $user['role'], $id);
                }
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare('INSERT INTO users (first_name, last_name, loginname, password, role) VALUES (?, ?, ?, ?, ?)');
                $stmt->bind_param('sssss', $user['first_name'], $user['last_name'], $user['loginname'], $hash, $user['role']);
            }
            $stmt->execute();
            $stmt->close();
            header('Location: users.php');
            exit;
        }
    }
}

$pageTitle  = $id > 0 ? 'Επεξεργασία χρήστη' : 'Προσθήκη νέου χρήστη';
$activePage = 'users';
require __DIR__ . '/includes/header.php';
?>

<div class="card">
  <?php if ($error !== ''): ?>
    <p class="error-message"><?= h($error) ?></p>
  <?php endif; ?>

  <form class="contact-form" action="user_form.php" method="post">
    <input type="hidden" name="id" value="<?= (int) $id ?>">

    <label for="first_name">Όνομα</label>
    <input type="text" id="first_name" name="first_name" required value="<?= h($user['first_name']) ?>">

    <label for="last_name">Επώνυμο</label>
    <input type="text" id="last_name" name="last_name" required value="<?= h($user['last_name']) ?>">

    <label for="loginname">Loginname (e-mail)</label>
    <input type="email" id="loginname" name="loginname" required value="<?= h($user['loginname']) ?>">

    <label for="password">Password<?= $id > 0 ? ' (αφήστε κενό για να μην αλλάξει)' : '' ?></label>
    <input type="password" id="password" name="password" <?= $id > 0 ? '' : 'required' ?>>

    <label for="role">Ρόλος</label>
    <select id="role" name="role">
      <option value="student" <?= $user['role'] === 'student' ? 'selected' : '' ?>>Student</option>
      <option value="tutor" <?= $user['role'] === 'tutor' ? 'selected' : '' ?>>Tutor</option>
    </select>

    <button type="submit"><?= $id > 0 ? 'Αποθήκευση αλλαγών' : 'Δημιουργία χρήστη' ?></button>
  </form>
</div>

<p><a href="users.php">&larr; Επιστροφή στους χρήστες</a></p>

<?php require __DIR__ . '/includes/footer.php'; ?>
