<?php
declare(strict_types=1);

require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/auth.php';

require_login();
$userId = current_user_id();

$bookId = (int)($_GET['id'] ?? 0);
if ($bookId <= 0) {
    header('Location: books.php');
    exit;
}

$stmt = $pdo->prepare(
    'SELECT b.id, b.title, a.name AS author_name
     FROM books b
     JOIN authors a ON a.id = b.author_id
     WHERE b.id = ? AND b.user_id = ?'
);
$stmt->execute([$bookId, $userId]);
$book = $stmt->fetch();

if (!$book) {
    header('Location: books.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('DELETE FROM books WHERE id = ? AND user_id = ?');
    $stmt->execute([$bookId, $userId]);
    header('Location: books.php');
    exit;
}

$page_title = 'Boek verwijderen';
require __DIR__ . '/includes/header.php';
?>

<h1>Boek verwijderen</h1>

<div class="card">
  <p>Weet je zeker dat je dit boek wilt verwijderen?</p>
  <p><strong><?= e((string)$book['title']) ?></strong> (<?= e((string)$book['author_name']) ?>)</p>

  <form method="post">
    <div class="actions">
      <button class="btn" type="submit">Ja, verwijderen</button>
      <a class="btn btn-secondary" href="books.php">Annuleren</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
