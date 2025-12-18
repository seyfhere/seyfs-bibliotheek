<?php
declare(strict_types=1);

require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/auth.php';

require_login();
$userId = current_user_id();

$q = trim((string)($_GET['q'] ?? ''));
$results = [];

if ($q !== '') {
    $stmt = $pdo->prepare(
        'SELECT b.id, b.title, b.publication_year, b.status, a.name AS author_name
         FROM books b
         JOIN authors a ON a.id = b.author_id
         WHERE b.user_id = ? AND b.title LIKE ?
         ORDER BY b.created_at DESC'
    );
    $stmt->execute([$userId, '%' . $q . '%']);
    $results = $stmt->fetchAll();
}

$page_title = 'Zoeken';
require __DIR__ . '/includes/header.php';
?>

<h1>Zoeken</h1>

<div class="card">
  <form method="get">
    <label for="q">Zoek op titel</label>
    <input id="q" name="q" value="<?= e($q) ?>" placeholder="Bijv. Clean Code">
    <div class="actions">
      <button class="btn" type="submit">Zoek</button>
      <a class="btn btn-secondary" href="search.php">Reset</a>
    </div>
  </form>
</div>

<?php if ($q !== ''): ?>
  <h2>Resultaten</h2>
  <?php if (!$results): ?>
    <p class="notice">Geen resultaten.</p>
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
          <?php foreach ($results as $r): ?>
            <tr>
              <td><?= e((string)$r['title']) ?></td>
              <td><?= e((string)$r['author_name']) ?></td>
              <td><?= e((string)($r['publication_year'] ?? '')) ?></td>
              <td><?= e((string)$r['status']) ?></td>
              <td><a href="book_edit.php?id=<?= (int)$r['id'] ?>">Bewerk</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
