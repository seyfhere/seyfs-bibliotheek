<?php
declare(strict_types=1);

require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/auth.php';

require_login();
$userId = current_user_id();

$errors = [];

// authors owned by user
$stmt = $pdo->prepare(
    'SELECT id, first_name, last_name
     FROM authors
     WHERE user_id = ?
     ORDER BY last_name'
);
$stmt->execute([$userId]);
$authors = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $authorId = (int)($_POST['author_id'] ?? 0);
    $status = $_POST['status'] ?? 'wishlist';

    if ($title === '') $errors[] = 'Titel is verplicht.';
    if ($authorId <= 0) $errors[] = 'Kies een auteur.';

    /* IMAGE UPLOAD */
    $coverPath = null;
    if (!empty($_FILES['cover']['name'])) {
        $ext = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('cover_', true) . '.' . $ext;
        $destination = 'assets/img/' . $filename;
        move_uploaded_file($_FILES['cover']['tmp_name'], $destination);
        $coverPath = $destination;
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            'INSERT INTO books (user_id, author_id, title, status, cover_image)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $authorId, $title, $status, $coverPath]);
        header('Location: books.php');
        exit;
    }
}

$page_title = 'Boek toevoegen';
require __DIR__ . '/includes/header.php';
?>

<h1>Boek toevoegen</h1>

<?php foreach ($errors as $e): ?>
  <p class="error"><?= e($e) ?></p>
<?php endforeach; ?>

<form method="post" enctype="multipart/form-data">
  <label>Titel</label>
  <input name="title" required>

  <label>Auteur</label>
  <select name="author_id" required>
    <option value="">-- kies --</option>
    <?php foreach ($authors as $a): ?>
      <option value="<?= $a['id'] ?>">
        <?= e($a['first_name'] . ' ' . $a['last_name']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <label>Status</label>
  <select name="status">
    <option value="wishlist">Wishlist</option>
    <option value="reading">Bezig</option>
    <option value="read">Gelezen</option>
  </select>

  <label>Boekcover</label>
  <input type="file" name="cover" accept="image/*">

  <button type="submit">Opslaan</button>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
