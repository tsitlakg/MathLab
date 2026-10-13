<?php
/**
 * users.php - User management
 * Lists the users (tutors and students) and lets a tutor add, edit and
 * delete them. Tutors only.
 */
require_once __DIR__ . '/includes/auth.php';
require_tutor();

// Load all users, tutors first
$db = get_db();
$result = $db->query('SELECT id, first_name, last_name, loginname, role FROM users ORDER BY role, last_name');

$pageTitle  = 'Χρήστες';
$activePage = 'users';
require __DIR__ . '/includes/header.php';
?>

<p>Διαχείριση των λογαριασμών Καθηγητών και Φοιτητών του ιστοχώρου.</p>

<p><a class="action-link" href="user_form.php">Προσθήκη νέου χρήστη</a></p>

<?php while ($row = $result->fetch_assoc()): ?>
  <div class="card">
    <h2>
      <?= h($row['first_name'] . ' ' . $row['last_name']) ?>
      <span class="row-actions">
        [<a href="user_delete.php?id=<?= (int) $row['id'] ?>" onclick="return confirm('Διαγραφή αυτού του χρήστη;');">διαγραφή</a>]
        [<a href="user_form.php?id=<?= (int) $row['id'] ?>">επεξεργασία</a>]
      </span>
    </h2>
    <p class="meta"><strong>Loginname (e-mail):</strong> <?= h($row['loginname']) ?></p>
    <p class="meta"><strong>Ρόλος:</strong> <?= $row['role'] === 'tutor' ? 'Καθηγητής' : 'Φοιτητής' ?></p>
  </div>
<?php endwhile; ?>

<a href="#top" class="back-to-top">&uarr; ↑</a>

<?php require __DIR__ . '/includes/footer.php'; ?>
