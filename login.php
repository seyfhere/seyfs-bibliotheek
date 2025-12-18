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

    if ($email === '' || $password === '') {
        $errors[] = 'Vul e-mail en wachtwoord in.';
    } else {
        $stmt = $pdo->prepare('SELECT id, email, password_hash FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, (string)$user['password_hash'])) {
            $errors[] = 'Onjuiste inloggegevens.';
        } else {
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['email'] = (string)$user['email'];
            header('Location: index.php');
            exit;
        }
    }
}

$page_title = 'Inloggen';
require __DIR__ . '/includes/header.php';
?>

<h1>Inloggen</h1>

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
      <button class="btn" type="submit">Inloggen</button>
      <a class="btn btn-secondary" href="register.php">Registreren</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
