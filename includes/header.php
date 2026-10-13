<?php
/**
 * header.php
 * Common header of all protected pages.
 * The including page must define these variables first:
 *   $pageTitle   (string) - page title (used in <title> and <h1>)
 *   $activePage  (string) - key of the active page for the menu (see menu.php)
 */
if (!isset($pageTitle)) {
    $pageTitle = 'MathLab';
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <title>MathLab - <?= h($pageTitle) ?></title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div id="page">

    <a id="top"></a>

    <div id="header">
      <p class="brand">MathLab</p>
      <h1 class="page-title"><?= h($pageTitle) ?></h1>
      <?php if (is_logged_in()): $u = current_user(); ?>
        <p class="session-info">
          Συνδεδεμένος ως <strong><?= h($u['first_name'] . ' ' . $u['last_name']) ?></strong>
          (<?= $u['role'] === 'tutor' ? 'Καθηγητής' : 'Φοιτητής' ?>) &middot;
          <a href="logout.php">Αποσύνδεση</a>
        </p>
      <?php endif; ?>
    </div>

    <div id="body-wrapper">
      <?php require __DIR__ . '/menu.php'; ?>
      <div id="content">
