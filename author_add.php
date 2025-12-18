<?php
declare(strict_types=1);

require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/auth.php';

require_login();
$userId = current_user_id();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));

    if ($name === '') {
        $errors[] = 'Naam is verplicht.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM authors WHERE user_id = ? AND name = ?');
        $stmt->execute([$userId, $name]);
        if ($stmt->fetch()) {
            $errors[] = 'Deze auteur bestaat al.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO authors (user_id, name) VALUES (?, ?)');
            $stmt->execute([$userId, $name]);
            header('Location: authors.php');
            exit;
        }
    }
}

$page_title = 'Auteur toevoegen';
require __DIR__ . '/includes/header.php';
?>

<h1>Auteur toevoegen</h1>

<?php if ($errors): ?>
  <div class="error">
    <ul>
      <?php foreach ($errors as $err): ?>
        <li><?= e($err) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="card">
  <form method="post" novalidate>
    <label for="name">Naam</label>
    <input id="name" name="name" required value="<?= e((string)($_POST['name'] ?? '')) ?>">

    <div class="actions">
      <button class="btn" type="submit">Opslaan</button>
      <a class="btn btn-secondary" href="authors.php">Terug</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
