<?php
declare(strict_types=1);

require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/auth.php';

require_login();
$userId = current_user_id();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['first_name'] ?? '');
    $last  = trim($_POST['last_name'] ?? '');

    if ($first === '' || $last === '') {
        $errors[] = 'Voornaam en achternaam zijn verplicht.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            'INSERT INTO authors (user_id, first_name, last_name)
             VALUES (?, ?, ?)'
        );
        $stmt->execute([$userId, $first, $last]);

        header('Location: authors.php');
        exit;
    }
}

$page_title = 'Auteur toevoegen';
require __DIR__ . '/includes/header.php';
?>

<h1>Auteur toevoegen</h1>

<?php if ($errors): ?>
  <div class="error">
    <ul>
      <?php foreach ($errors as $e): ?>
        <li><?= e($e) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post">
  <label>Voornaam</label>
  <input name="first_name" required>

  <label>Achternaam</label>
  <input name="last_name" required>

  <button type="submit">Opslaan</button>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
