<?php
declare(strict_types=1);

require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/auth.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Voer een geldig e-mailadres in.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Wachtwoord moet minimaal 6 tekens zijn.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Dit e-mailadres bestaat al.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (email, password_hash) VALUES (?, ?)');
            $stmt->execute([$email, $hash]);

            $userId = (int)$pdo->lastInsertId();
            $_SESSION['user_id'] = $userId;
            $_SESSION['email'] = $email;

            header('Location: index.php');
            exit;
        }
    }
}

$page_title = 'Registreren';
require __DIR__ . '/includes/header.php';
?>

<h1>Registreren</h1>

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
    <label for="email">E-mail</label>
    <input id="email" name="email" type="email" required value="<?= e((string)($_POST['email'] ?? '')) ?>">

    <label for="password">Wachtwoord</label>
    <input id="password" name="password" type="password" required>

    <div class="actions">
      <button class="btn" type="submit">Account maken</button>
      <a class="btn btn-secondary" href="login.php">Inloggen</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
