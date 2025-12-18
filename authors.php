<?php
declare(strict_types=1);

require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/auth.php';

require_login();
$userId = current_user_id();

$stmt = $pdo->prepare(
    'SELECT a.id, a.name, COUNT(b.id) AS book_count
     FROM authors a
     LEFT JOIN books b ON b.author_id = a.id AND b.user_id = a.user_id
     WHERE a.user_id = ?
     GROUP BY a.id, a.name
     ORDER BY a.name ASC'
);
$stmt->execute([$userId]);
$authors = $stmt->fetchAll();

$page_title = 'Auteurs';
require __DIR__ . '/includes/header.php';
?>

<h1>Auteurs</h1>

<div class="actions">
  <a class="btn" href="author_add.php">Auteur toevoegen</a>
</div>

<?php if (!$authors): ?>
  <p class="notice">Nog geen auteurs toegevoegd.</p>
<?php else: ?>
  <div class="card">
    <table>
      <thead>
        <tr>
          <th>Auteur</th>
          <th>Aantal boeken</th>
          <th>Boeken bekijken</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($authors as $a): ?>
          <tr>
            <td><?= e((string)$a['name']) ?></td>
            <td><?= (int)$a['book_count'] ?></td>
            <td><a href="authors.php?author_id=<?= (int)$a['id'] ?>">Toon boeken</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php
$authorId = (int)($_GET['author_id'] ?? 0);
if ($authorId > 0) {
    $stmt = $pdo->prepare('SELECT id, name FROM authors WHERE id = ? AND user_id = ?');
    $stmt->execute([$authorId, $userId]);
    $author = $stmt->fetch();

    if ($author) {
        $stmt = $pdo->prepare(
            'SELECT b.id, b.title, b.publication_year, b.status
             FROM books b
             WHERE b.user_id = ? AND b.author_id = ?
             ORDER BY b.created_at DESC'
        );
        $stmt->execute([$userId, $authorId]);
        $books = $stmt->fetchAll();
        ?>
        <h2>Boeken van <?= e((string)$author['name']) ?></h2>
        <?php if (!$books): ?>
          <p class="notice">Geen boeken voor deze auteur.</p>
        <?php else: ?>
          <div class="card">
            <table>
              <thead>
                <tr>
                  <th>Titel</th>
                  <th>Jaar</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($books as $b): ?>
                  <tr>
                    <td><?= e((string)$b['title']) ?></td>
                    <td><?= e((string)($b['publication_year'] ?? '')) ?></td>
                    <td><?= e((string)$b['status']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
        <?php
    }
}
?>

<?php require __DIR__ . '/includes/footer.php'; ?>
