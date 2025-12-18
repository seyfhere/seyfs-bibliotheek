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
    'SELECT id, author_id, title, publication_year, status
     FROM books
     WHERE id = ? AND user_id = ?'
);
$stmt->execute([$bookId, $userId]);
$book = $stmt->fetch();

if (!$book) {
    header('Location: books.php');
    exit;
}

$stmt = $pdo->prepare('SELECT id, name FROM authors WHERE user_id = ? ORDER BY name ASC');
$stmt->execute([$userId]);
$authors = $stmt->fetchAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim((string)($_POST['title'] ?? ''));
    $authorId = (int)($_POST['author_id'] ?? 0);
    $year = trim((string)($_POST['publication_year'] ?? ''));
    $status = (string)($_POST['status'] ?? 'wishlist');

    if ($title === '') {
        $errors[] = 'Titel is verplicht.';
    }
    if ($authorId <= 0) {
        $errors[] = 'Kies een auteur.';
    }

    $validStatus = ['wishlist', 'reading', 'read'];
    if (!in_array($status, $validStatus, true)) {
        $status = 'wishlist';
    }

    $yearVal = null;
    if ($year !== '') {
        if (!ctype_digit($year) || (int)$year < 0 || (int)$year > 3000) {
            $errors[] = 'Publicatiejaar is ongeldig.';
        } else {
            $yearVal = (int)$year;
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM authors WHERE id = ? AND user_id = ?');
        $stmt->execute([$authorId, $userId]);
        if (!$stmt->fetch()) {
            $errors[] = 'Auteur bestaat niet.';
        } else {
            $stmt = $pdo->prepare(
                'UPDATE books
                 SET author_id = ?, title = ?, publication_year = ?, status = ?, updated_at = CURRENT_TIMESTAMP
                 WHERE id = ? AND user_id = ?'
            );
            $stmt->execute([$authorId, $title, $yearVal, $status, $bookId, $userId]);
            header('Location: books.php');
            exit;
        }
    }
}

$page_title = 'Boek bewerken';
require __DIR__ . '/includes/header.php';

$formTitle = (string)($_POST['title'] ?? $book['title']);
$formAuthorId = (int)($_POST['author_id'] ?? $book['author_id']);
$formYear = (string)($_POST['publication_year'] ?? ($book['publication_year'] ?? ''));
$formStatus = (string)($_POST['status'] ?? $book['status']);
?>

<h1>Boek bewerken</h1>

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
    <label for="title">Titel</label>
    <input id="title" name="title" required value="<?= e($formTitle) ?>">

    <label for="author_id">Auteur</label>
    <select id="author_id" name="author_id" required>
      <option value="">Kies een auteur</option>
      <?php foreach ($authors as $a): ?>
        <option value="<?= (int)$a['id'] ?>" <?= ($formAuthorId === (int)$a['id']) ? 'selected' : '' ?>>
          <?= e((string)$a['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <div class="row">
      <div>
        <label for="publication_year">Publicatiejaar (optioneel)</label>
        <input id="publication_year" name="publication_year" inputmode="numeric" value="<?= e($formYear) ?>">
      </div>
      <div>
        <label for="status">Status</label>
        <select id="status" name="status">
          <option value="wishlist" <?= ($formStatus === 'wishlist') ? 'selected' : '' ?>>Wishlist</option>
          <option value="reading" <?= ($formStatus === 'reading') ? 'selected' : '' ?>>Bezig</option>
          <option value="read" <?= ($formStatus === 'read') ? 'selected' : '' ?>>Gelezen</option>
        </select>
      </div>
    </div>

    <div class="actions">
      <button class="btn" type="submit">Opslaan</button>
      <a class="btn btn-secondary" href="books.php">Terug</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
