<?php
declare(strict_types=1);

require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/auth.php';

require_login();

$stmt = $pdo->query(
    'SELECT DISTINCT a.id, a.first_name, a.last_name
     FROM authors a
     ORDER BY a.last_name'
);
$authors = $stmt->fetchAll();

$page_title = 'Auteurs';
require __DIR__ . '/includes/header.php';
?>

<h1>Auteurs</h1>

<a href="author_add.php">Auteur toevoegen</a>

<ul>
<?php foreach ($authors as $a): ?>
  <li>
    <?= e($a['first_name'] . ' ' . $a['last_name']) ?>
    <ul>
      <?php
      $stmt = $pdo->prepare(
          'SELECT title, cover_image
           FROM books
           WHERE author_id = ?'
      );
      $stmt->execute([$a['id']]);
      foreach ($stmt as $b):
      ?>
        <li>
          <?php if ($b['cover_image']): ?>
            <img src="<?= e($b['cover_image']) ?>" width="40">
          <?php endif; ?>
          <?= e($b['title']) ?>
        </li>
      <?php endforeach; ?>
    </ul>
  </li>
<?php endforeach; ?>
</ul>

<?php require __DIR__ . '/includes/footer.php'; ?>
