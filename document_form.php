<?php
/**
 * document_form.php
 * Form for adding a new course document, including uploading the file
 * to the server. Tutors only.
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/upload.php';
require_tutor();

$error = '';

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read and clean the submitted values
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title === '' || empty($_FILES['file']['name'])) {
        $error = 'Παρακαλώ συμπληρώστε τον τίτλο και επιλέξτε ένα αρχείο.';
    } elseif ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Παρουσιάστηκε πρόβλημα κατά το ανέβασμα του αρχείου.';
    } elseif (($safeName = safe_upload_name($_FILES['file']['name'])) === null) {
        $error = 'Μη επιτρεπτός τύπος αρχείου. Επιτρέπονται: ' . implode(', ', ALLOWED_UPLOAD_EXTENSIONS) . '.';
    } else {
        $destination  = UPLOAD_DIR_DOCUMENTS . $safeName;

        // Create the upload folder if it does not exist yet
        if (!is_dir(UPLOAD_DIR_DOCUMENTS)) {
            mkdir(UPLOAD_DIR_DOCUMENTS, 0755, true);
        }

        // Move the file from PHP's temp folder to files/ and save its path in the database
        if (move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {
            $relativePath = 'files/' . $safeName;
            $db = get_db();
            $stmt = $db->prepare('INSERT INTO documents (title, description, file_path) VALUES (?, ?, ?)');
            $stmt->bind_param('sss', $title, $description, $relativePath);
            $stmt->execute();
            $stmt->close();
            header('Location: documents.php');
            exit;
        }
        $error = 'Δεν ήταν δυνατή η αποθήκευση του αρχείου στον διακομιστή.';
    }
}

$pageTitle  = 'Προσθήκη νέου εγγράφου';
$activePage = 'documents';
require __DIR__ . '/includes/header.php';
?>

<div class="card">
  <?php if ($error !== ''): ?>
    <p class="error-message"><?= h($error) ?></p>
  <?php endif; ?>

  <form class="contact-form" action="document_form.php" method="post" enctype="multipart/form-data">
    <label for="title">Τίτλος εγγράφου</label>
    <input type="text" id="title" name="title" required value="<?= h($_POST['title'] ?? '') ?>">

    <label for="description">Περιγραφή</label>
    <textarea id="description" name="description"><?= h($_POST['description'] ?? '') ?></textarea>

    <label for="file">Αρχείο</label>
    <input type="file" id="file" name="file" required>

    <button type="submit">Ανέβασμα εγγράφου</button>
  </form>
</div>

<p><a href="documents.php">&larr; Επιστροφή στα έγγραφα μαθήματος</a></p>

<?php require __DIR__ . '/includes/footer.php'; ?>
