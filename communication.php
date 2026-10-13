<?php
/**
 * communication.php - Contact page
 * Shows a web form for sending a message (processed by send_mail.php,
 * which e-mails every user with the tutor role) and, as an alternative,
 * a mailto link.
 */
require_once __DIR__ . '/includes/auth.php';
require_login();

$sent = isset($_GET['sent']);

$pageTitle  = 'Επικοινωνία';
$activePage = 'communication';
require __DIR__ . '/includes/header.php';
?>

<p>
  Για οποιαδήποτε απορία ή διευκρίνιση σχετικά με το μάθημα, μπορείτε
  να επικοινωνήσετε με τον διδάσκοντα χρησιμοποιώντας έναν από τους
  παρακάτω τρόπους:
</p>
<ul>
  <li>Μέσω web φόρμας</li>
  <li>Με χρήση e-mail διεύθυνσης</li>
</ul>

<h2>Αποστολή e-mail μέσω web φόρμας</h2>

<?php if ($sent): ?>
  <p class="success-message">Το μήνυμά σας εστάλη στους διδάσκοντες του μαθήματος.</p>
<?php endif; ?>

<div class="card">
  <form class="contact-form" action="send_mail.php" method="post">
    <label for="sender">Αποστολέας</label>
    <input type="email" id="sender" name="sender" placeholder="e-mail αποστολέα" required>

    <label for="subject">Θέμα</label>
    <input type="text" id="subject" name="subject" placeholder="Θέμα μηνύματος" required>

    <label for="message">Κείμενο</label>
    <textarea id="message" name="message" placeholder="Γράψτε εδώ το μήνυμά σας..." required></textarea>

    <button type="submit">Αποστολή</button>
  </form>
</div>

<h2>Αποστολή e-mail με χρήση e-mail διεύθυνσης</h2>

<p>Εναλλακτικά μπορείτε να αποστείλετε e-mail στην παρακάτω διεύθυνση ηλεκτρονικού ταχυδρομείου:</p>

<div class="mail-box">
  <a href="mailto:tutor@mathlab.tutor.gr">tutor@mathlab.tutor.gr</a>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
