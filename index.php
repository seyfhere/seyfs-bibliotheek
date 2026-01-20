<?php
declare(strict_types=1);

require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/auth.php';

require_login();

$stmt = $pdo->query(
    'SELECT u.email, b.title
     FROM books b
     JOIN users u ON u.id = b.user_id
     ORDER BY b.created_at DESC
     LIMIT 5'
);
$activity = $stmt->fetchAll();

$page_title = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>

<h1>Dashboard</h1>

<h2>Snelle links</h2>
<ul>
  <li><a href="books.php">Boeken</a></li>
  <li><a href="authors.php">Auteurs</a></li>
  <li><a href="search.php">Zoeken</a></li>
</ul>

<h2>Recente activiteit</h2>
<ul>
<?php foreach ($activity as $a): ?>
  <li><?= e($a['email']) ?> heeft “<?= e($a['title']) ?>” toegevoegd</li>
<?php endforeach; ?>
</ul>

<?php require __DIR__ . '/includes/footer.php'; ?>
