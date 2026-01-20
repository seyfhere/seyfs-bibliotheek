<?php
declare(strict_types=1);

require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/auth.php';

require_login();

/* AUTHORS FOR DROPDOWN (GLOBAL) */
$stmt = $pdo->query(
    'SELECT id, first_name, last_name
     FROM authors
     ORDER BY last_name, first_name'
);
$authors = $stmt->fetchAll();

/* INPUT */
$q = trim($_GET['q'] ?? '');
$authorId = (int)($_GET['author_id'] ?? 0);

/* BUILD QUERY */
$sql = '
SELECT b.id, b.title, b.cover_image,
       a.first_name, a.last_name
FROM books b
JOIN authors a ON a.id = b.author_id
WHERE 1=1
';

$params = [];

if ($q !== '') {
    $sql .= ' AND b.title LIKE ?';
    $params[] = '%' . $q . '%';
}

if ($authorId > 0) {
    $sql .= ' AND a.id = ?';
    $params[] = $authorId;
}

$sql .= ' ORDER BY b.created_at DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();

$page_title = 'Zoeken';
require __DIR__ . '/includes/header.php';
?>

<h1>Zoeken</h1>

<form method="get">
  <label>Zoek op titel</label>
  <input name="q" value="<?= e($q) ?>">

  <label>Auteur</label>
  <select name="author_id">
    <option value="">Alle auteurs</option>
    <?php foreach ($authors as $a): ?>
      <option value="<?= $a['id'] ?>"
        <?= $authorId === (int)$a['id'] ? 'selected' : '' ?>>
        <?= e($a['first_name'] . ' ' . $a['last_name']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <button type="submit">Zoek</button>
</form>

<h2>Resultaten</h2>

<?php if (!$results): ?>
  <p>Geen resultaten.</p>
<?php else: ?>
  <ul>
  <?php foreach ($results as $r): ?>
    <li>
      <?php if ($r['cover_image']): ?>
        <img src="<?= e($r['cover_image']) ?>" width="40">
      <?php endif; ?>
      <?= e($r['title']) ?> —
      <?= e($r['first_name'] . ' ' . $r['last_name']) ?>
    </li>
  <?php endforeach; ?>
  </ul>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
