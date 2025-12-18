<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($page_title) ? htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') : 'Seyf’s Bibliotheek' ?></title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="topbar">
  <div class="container topbar-inner">
    <div class="brand"><a href="index.php">Seyf’s Bibliotheek</a></div>
    <nav class="nav">
      <?php if (!empty($_SESSION['user_id'])): ?>
        <a href="index.php">Dashboard</a>
        <a href="books.php">Boeken</a>
        <a href="authors.php">Auteurs</a>
        <a href="search.php">Zoeken</a>
        <a href="logout.php">Uitloggen</a>
      <?php else: ?>
        <a href="index.html">Home</a>
        <a href="login.php">Inloggen</a>
        <a href="register.php">Registreren</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main class="container">
