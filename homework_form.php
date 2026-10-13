<?php
/**
 * homework_form.php
 * Form for adding a new assignment (objectives, assignment file to
 * upload, deliverables, due date). Tutors only.
 *
 * When a new assignment is added, a matching row is also created
 * automatically in the announcements table.
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/upload.php';
require_tutor();

$error = '';

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read and clean the submitted values
    $objectives   = trim($_POST['objectives'] ?? '');
    $deliverables = trim($_POST['deliverables'] ?? '');
    $dueDate      = $_POST['due_date'] ?? '';

    if ($objectives === '' || $deliverables === '' || $dueDate === '' || empty($_FILES['assignment_file']['name'])) {
        $error = 'Παρακαλώ συμπληρώστε όλα τα πεδία και επιλέξτε το αρχείο εκφώνησης.';
    } elseif ($_FILES['assignment_file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Παρουσιάστηκε πρόβλημα κατά το ανέβασμα του αρχείου εκφώνησης.';
    } elseif (($safeName = safe_upload_name($_FILES['assignment_file']['name'])) === null) {
        $error = 'Μη επιτρεπτός τύπος αρχείου. Επιτρέπονται: ' . implode(', ', ALLOWED_UPLOAD_EXTENSIONS) . '.';
    } else {
        $destination  = UPLOAD_DIR_HOMEWORK . $safeName;

        // Create the upload folder if it does not exist yet
        if (!is_dir(UPLOAD_DIR_HOMEWORK)) {
            mkdir(UPLOAD_DIR_HOMEWORK, 0755, true);
        }

        if (move_uploaded_file($_FILES['assignment_file']['tmp_name'], $destination)) {
            $relativePath = 'assignments/' . $safeName;

            $db = get_db();
            // Both inserts (assignment + announcement) succeed or both are undone
            $db->begin_transaction();
            try {
                $stmt = $db->prepare('INSERT INTO homework (objectives, assignment_file, deliverables, due_date) VALUES (?, ?, ?, ?)');
                $stmt->bind_param('ssss', $objectives, $relativePath, $deliverables, $dueDate);
                $stmt->execute();
                $homeworkId = $db->insert_id;
                $stmt->close();

                // Automatically create an announcement for the new assignment
                // (its text is Greek on purpose: it is shown to users)
                $today       = date('Y-m-d');
                $subject     = 'Υποβλήθηκε η εργασία ' . $homeworkId;
                $body        = 'Η ημερομηνία παράδοσης της εργασίας είναι ' . date('d/m/Y', strtotime($dueDate));
                $stmt = $db->prepare('INSERT INTO announcements (announcement_date, subject, body) VALUES (?, ?, ?)');
                $stmt->bind_param('sss', $today, $subject, $body);
                $stmt->execute();
                $stmt->close();

                $db->commit();
                header('Location: homework.php');
                exit;
            } catch (Throwable $e) {
                $db->rollback();
                $error = 'Παρουσιάστηκε σφάλμα κατά την αποθήκευση. Δοκιμάστε ξανά.';
            }
        } else {
            $error = 'Δεν ήταν δυνατή η αποθήκευση του αρχείου στον διακομιστή.';
        }
    }
}

$pageTitle  = 'Προσθήκη νέας εργασίας';
$activePage = 'homework';
require __DIR__ . '/includes/header.php';
?>

<div class="card">
  <?php if ($error !== ''): ?>
    <p class="error-message"><?= h($error) ?></p>
  <?php endif; ?>

  <form class="contact-form" action="homework_form.php" method="post" enctype="multipart/form-data">
    <label for="objectives">Στόχοι (ένας ανά γραμμή)</label>
    <textarea id="objectives" name="objectives" required placeholder="π.χ.&#10;Στόχος 1&#10;Στόχος 2"><?= h($_POST['objectives'] ?? '') ?></textarea>

    <label for="assignment_file">Αρχείο εκφώνησης</label>
    <input type="file" id="assignment_file" name="assignment_file" required>

    <label for="deliverables">Παραδοτέα (ένα ανά γραμμή)</label>
    <textarea id="deliverables" name="deliverables" required placeholder="Γραπτή αναφορά σε word&#10;Παρουσίαση σε powerpoint"><?= h($_POST['deliverables'] ?? '') ?></textarea>

    <label for="due_date">Ημερομηνία παράδοσης</label>
    <input type="date" id="due_date" name="due_date" required value="<?= h($_POST['due_date'] ?? '') ?>">

    <button type="submit">Δημοσίευση εργασίας</button>
  </form>
</div>

<p><a href="homework.php">&larr; Επιστροφή στις εργασίες</a></p>

<?php require __DIR__ . '/includes/footer.php'; ?>
