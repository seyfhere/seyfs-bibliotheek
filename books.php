<?php
declare(strict_types=1);

require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/auth.php';

require_login();

$stmt = $pdo->query(
    'SELECT b.id, b.title, b.cover_image, b.user_id,
            a.first_name, a.last_name
     FROM books b
     JOIN authors a ON a.id = b.author_id
     ORDER BY b.created_at DESC'
);
$books = $stmt->fetchAll();

$page_title = 'Boeken';
require __DIR__ . '/includes/header.php';
?>

<h1>Boeken</h1>

<a href="book_add.php">Boek toevoegen</a>

<ul>
<?php foreach ($books as $b): ?>
  <li>
    <?php if ($b['cover_image']): ?>
      <img src="<?= e($b['cover_image']) ?>" width="50">
    <?php endif; ?>
    <?= e($b['title']) ?> —
    <?= e($b['first_name'] . ' ' . $b['last_name']) ?>

    <?php if ($b['user_id'] === $_SESSION['user_id']): ?>
      | <a href="book_edit.php?id=<?= $b['id'] ?>">Bewerk</a>
      | <a href="book_delete.php?id=<?= $b['id'] ?>">Verwijder</a>
    <?php endif; ?>
  </li>
<?php endforeach; ?>
</ul>

<?php require __DIR__ . '/includes/footer.php'; ?>
