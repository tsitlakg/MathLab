<?php
/**
 * announcement.php - Announcements
 * Shows the course announcements stored in the database.
 * Students only see the list. Tutors also see links to add, edit and
 * delete announcements.
 */
require_once __DIR__ . '/includes/auth.php';
require_login();

// Load all announcements, newest first
$db = get_db();
$result = $db->query('SELECT id, announcement_date, subject, body FROM announcements ORDER BY announcement_date DESC, id DESC');

/** Turns the name of the Assignments page, when it appears in an announcement text, into a link to homework.php. */
function linkify_homework(string $text): string
{
    $escaped = h($text);
    return str_replace('«Εργασίες»', '<a href="homework.php">«Εργασίες»</a>', $escaped);
}

$pageTitle  = 'Ανακοινώσεις';
$activePage = 'announcement';
require __DIR__ . '/includes/header.php';
?>

<?php if (is_tutor()): ?>
  <p><a class="action-link" href="announcement_form.php">Προσθήκη νέας ανακοίνωσης</a></p>
<?php endif; ?>

<?php while ($row = $result->fetch_assoc()): ?>
  <div class="card">
    <h2>
      Ανακοίνωση <?= (int) $row['id'] ?>
      <?php if (is_tutor()): ?>
        <span class="row-actions">
          [<a href="announcement_delete.php?id=<?= (int) $row['id'] ?>" onclick="return confirm('Διαγραφή αυτής της ανακοίνωσης;');">διαγραφή</a>]
          [<a href="announcement_form.php?id=<?= (int) $row['id'] ?>">επεξεργασία</a>]
        </span>
      <?php endif; ?>
    </h2>
    <p class="meta"><strong>Ημερομηνία</strong>: <?= h(date('d/m/Y', strtotime($row['announcement_date']))) ?></p>
    <p class="meta"><strong>Θέμα</strong>: <?= h($row['subject']) ?></p>
    <p><?= linkify_homework($row['body']) ?></p>
  </div>
<?php endwhile; ?>

<a href="#top" class="back-to-top">&uarr; ↑</a>

<?php require __DIR__ . '/includes/footer.php'; ?>
