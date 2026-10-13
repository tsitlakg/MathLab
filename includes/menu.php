<?php
/**
 * menu.php
 * Vertical navigation menu shared by all pages.
 */
if (!isset($activePage)) {
    $activePage = '';
}

$items = [
    'index'        => ['href' => 'index.php',         'icon' => 'btn-home.png',          'label' => 'Αρχική σελίδα'],
    'announcement' => ['href' => 'announcement.php',  'icon' => 'btn-announcements.png', 'label' => 'Ανακοινώσεις'],
    'communication'=> ['href' => 'communication.php', 'icon' => 'btn-communication.png', 'label' => 'Επικοινωνία'],
    'documents'    => ['href' => 'documents.php',     'icon' => 'btn-documents.png',     'label' => 'Έγγραφα μαθήματος'],
    'homework'     => ['href' => 'homework.php',      'icon' => 'btn-homework.png',      'label' => 'Εργασίες'],
];
if (is_tutor()) {
    $items['users'] = ['href' => 'users.php', 'icon' => 'btn-users.png', 'label' => 'Χρήστες'];
}
?>
<nav id="menu">
  <?php foreach ($items as $key => $item): ?>
    <a href="<?= h($item['href']) ?>" class="<?= $key === $activePage ? 'active' : '' ?>">
      <img src="images/<?= h($item['icon']) ?>" alt="<?= h($item['label']) ?>">
    </a>
  <?php endforeach; ?>
</nav>