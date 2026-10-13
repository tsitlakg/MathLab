<?php
/**
 * homework.php - Assignments
 * Students see the course assignments (objectives, assignment file to
 * download, deliverables, due date). Tutors also see a link for adding
 * a new assignment.
 */
require_once __DIR__ . '/includes/auth.php';
require_login();

// Load all assignments, ordered by due date
$db = get_db();
$result = $db->query('SELECT id, objectives, assignment_file, deliverables, due_date FROM homework ORDER BY due_date');

$pageTitle  = 'Εργασίες';
$activePage = 'homework';
require __DIR__ . '/includes/header.php';
?>

<p>Στη σελίδα αυτή θα βρείτε τις εκφωνήσεις των εργασιών του μαθήματος τις οποίες μπορείτε να κατεβάσετε, καθώς και αναλυτικές πληροφορίες σχετικά με την παράδοσή τους.</p>

<?php if (is_tutor()): ?>
  <p><a class="action-link" href="homework_form.php">Προσθήκη νέας εργασίας</a></p>
<?php endif; ?>

<?php while ($row = $result->fetch_assoc()): ?>
  <div class="card">
    <h2>
      Εργασία <?= (int) $row['id'] ?>
      <?php if (is_tutor()): ?>
        <span class="row-actions">
          [<a href="homework_delete.php?id=<?= (int) $row['id'] ?>" onclick="return confirm('Διαγραφή αυτής της εργασίας;');">διαγραφή</a>]
        </span>
      <?php endif; ?>
    </h2>

    <p class="meta"><em>Στόχοι:</em> Οι στόχοι της εργασίας είναι:</p>
    <ol>
      <?php foreach (explode("\n", trim($row['objectives'])) as $goal): ?>
        <?php if (trim($goal) !== ''): ?>
          <li><?= h(trim($goal)) ?></li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ol>

    <p class="meta"><em>Εκφώνηση:</em></p>
    <p>Κατεβάστε την εκφώνηση της εργασίας από <a href="<?= h($row['assignment_file']) ?>" download>εδώ</a></p>

    <p class="meta"><em>Παραδοτέα:</em></p>
    <ol>
      <?php foreach (explode("\n", trim($row['deliverables'])) as $item): ?>
        <?php if (trim($item) !== ''): ?>
          <li><?= h(trim($item)) ?></li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ol>

    <p class="meta"><strong>Ημερομηνία παράδοσης:</strong> <?= h(date('d/m/Y', strtotime($row['due_date']))) ?></p>
  </div>
<?php endwhile; ?>

<a href="#top" class="back-to-top">&uarr; ↑</a>

<?php require __DIR__ . '/includes/footer.php'; ?>
