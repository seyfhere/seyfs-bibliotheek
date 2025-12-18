<?php
declare(strict_types=1);

require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/auth.php';

require_login();
$userId = current_user_id();

$stmt = $pdo->prepare('SELECT COUNT(*) AS c FROM books WHERE user_id = ?');
$stmt->execute([$userId]);
$bookCount = (int)($stmt->fetch()['c'] ?? 0);

$stmt = $pdo->prepare('SELECT COUNT(*) AS c FROM authors WHERE user_id = ?');
$stmt->execute([$userId]);
$authorCount = (int)($stmt->fetch()['c'] ?? 0);

$page_title = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>

<h1>Dashboard</h1>

<div class="grid">
  <div class="card">
    <p><strong>Ingelogd als:</strong> <?= e((string)($_SESSION['email'] ?? '')) ?></p>
    <p><strong>Boeken:</strong> <?= $bookCount ?></p>
    <p><strong>Auteurs:</strong> <?= $authorCount ?></p>
    <div class="actions">
      <a class="btn" href="book_add.php">Boek toevoegen</a>
      <a class="btn btn-secondary" href="author_add.php">Auteur toevoegen</a>
    </div>
  </div>

  <div class="card">
    <h2>Snelle links</h2>
    <ul>
      <li><a href="books.php">Alle boeken</a></li>
      <li><a href="authors.php">Alle auteurs</a></li>
      <li><a href="search.php">Zoeken</a></li>
    </ul>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
