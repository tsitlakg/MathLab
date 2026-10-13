<?php
/**
 * announcement_form.php
 * Form for creating a new announcement or editing an existing one
 * (when ?id= is given). Tutors only.
 */
require_once __DIR__ . '/includes/auth.php';
require_tutor();

$db = get_db();
$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);

$announcement = ['announcement_date' => date('Y-m-d'), 'subject' => '', 'body' => ''];
$error = '';

if ($id > 0) {
    $stmt = $db->prepare('SELECT announcement_date, subject, body FROM announcements WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $found = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($found) {
        $announcement = $found;
    } else {
        $id = 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $announcement['announcement_date'] = $_POST['announcement_date'] ?? '';
    $announcement['subject']           = trim($_POST['subject'] ?? '');
    $announcement['body']              = trim($_POST['body'] ?? '');

    if ($announcement['announcement_date'] === '' || $announcement['subject'] === '' || $announcement['body'] === '') {
        $error = 'Παρακαλώ συμπληρώστε όλα τα πεδία.';
    } else {
        if ($id > 0) {
            $stmt = $db->prepare('UPDATE announcements SET announcement_date = ?, subject = ?, body = ? WHERE id = ?');
            $stmt->bind_param('sssi', $announcement['announcement_date'], $announcement['subject'], $announcement['body'], $id);
        } else {
            $stmt = $db->prepare('INSERT INTO announcements (announcement_date, subject, body) VALUES (?, ?, ?)');
            $stmt->bind_param('sss', $announcement['announcement_date'], $announcement['subject'], $announcement['body']);
        }
        $stmt->execute();
        $stmt->close();
        header('Location: announcement.php');
        exit;
    }
}

$pageTitle  = $id > 0 ? 'Επεξεργασία ανακοίνωσης' : 'Προσθήκη νέας ανακοίνωσης';
$activePage = 'announcement';
require __DIR__ . '/includes/header.php';
?>

<div class="card">
  <?php if ($error !== ''): ?>
    <p class="error-message"><?= h($error) ?></p>
  <?php endif; ?>

  <form class="contact-form" action="announcement_form.php" method="post">
    <input type="hidden" name="id" value="<?= (int) $id ?>">

    <label for="announcement_date">Ημερομηνία</label>
    <input type="date" id="announcement_date" name="announcement_date" required
           value="<?= h($announcement['announcement_date']) ?>">

    <label for="subject">Θέμα</label>
    <input type="text" id="subject" name="subject" required
           value="<?= h($announcement['subject']) ?>">

    <label for="body">Κυρίως κείμενο</label>
    <textarea id="body" name="body" required><?= h($announcement['body']) ?></textarea>

    <button type="submit"><?= $id > 0 ? 'Αποθήκευση αλλαγών' : 'Δημοσίευση ανακοίνωσης' ?></button>
  </form>
</div>

<p><a href="announcement.php">&larr; Επιστροφή στις ανακοινώσεις</a></p>

<?php require __DIR__ . '/includes/footer.php'; ?>
