<?php
declare(strict_types=1);

require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/auth.php';

require_login();
$userId = current_user_id();

$stmt = $pdo->prepare(
    'SELECT b.id, b.title, b.publication_year, b.status, a.name AS author_name
     FROM books b
     JOIN authors a ON a.id = b.author_id
     WHERE b.user_id = ?
     ORDER BY b.created_at DESC'
);
$stmt->execute([$userId]);
$books = $stmt->fetchAll();

$page_title = 'Boeken';
require __DIR__ . '/includes/header.php';
?>

<h1>Boeken</h1>

<div class="actions">
  <a class="btn" href="book_add.php">Boek toevoegen</a>
</div>

<?php if (!$books): ?>
  <p class="notice">Nog geen boeken toegevoegd.</p>
<?php else: ?>
  <div class="card">
    <table>
      <thead>
        <tr>
          <th>Titel</th>
          <th>Auteur</th>
          <th>Jaar</th>
          <th>Status</th>
          <th>Acties</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($books as $b): ?>
          <tr>
            <td><?= e((string)$b['title']) ?></td>
            <td><?= e((string)$b['author_name']) ?></td>
            <td><?= e((string)($b['publication_year'] ?? '')) ?></td>
            <td><?= e((string)$b['status']) ?></td>
            <td>
              <a href="book_edit.php?id=<?= (int)$b['id'] ?>">Bewerk</a>
              |
              <a href="book_delete.php?id=<?= (int)$b['id'] ?>">Verwijder</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
