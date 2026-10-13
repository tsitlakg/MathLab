<?php
/**
 * documents.php - Course documents
 * Students see the documents available for download. Tutors also see a
 * link for adding a new document (file upload).
 */
require_once __DIR__ . '/includes/auth.php';
require_login();

// Load all course documents
$db = get_db();
$result = $db->query('SELECT id, title, description, file_path FROM documents ORDER BY id');

$pageTitle  = 'Έγγραφα μαθήματος';
$activePage = 'documents';
require __DIR__ . '/includes/header.php';
?>

<p>Στη σελίδα αυτή θα βρείτε χρήσιμο υλικό σχετικά με το μάθημα, τα οποία μπορείτε να κατεβάσετε (download).</p>

<?php if (is_tutor()): ?>
  <p><a class="action-link" href="document_form.php">Προσθήκη νέου εγγράφου</a></p>
<?php endif; ?>

<?php while ($row = $result->fetch_assoc()): ?>
  <div class="card">
    <h2>
      <?= h($row['title']) ?>
      <?php if (is_tutor()): ?>
        <span class="row-actions">
          [<a href="document_delete.php?id=<?= (int) $row['id'] ?>" onclick="return confirm('Διαγραφή αυτού του εγγράφου;');">διαγραφή</a>]
        </span>
      <?php endif; ?>
    </h2>
    <p class="meta"><em>Περιγραφή:</em> <?= h($row['description']) ?></p>
    <a href="<?= h($row['file_path']) ?>" class="download-link" download>&#8595; Download</a>
  </div>
<?php endwhile; ?>

<a href="#top" class="back-to-top">&uarr; ↑</a>

<?php require __DIR__ . '/includes/footer.php'; ?>
