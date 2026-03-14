<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? APP_NAME, ENT_QUOTES, 'UTF-8') ?> – <?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <a href="/" class="logo"><?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?></a>
        <nav class="main-nav">
            <ul>
                <li><a href="/">Accueil</a></li>
                <li><a href="/programmations">Programmations</a></li>
                <li><a href="/pieces">Pièces</a></li>
                <li><a href="/lieux">Lieux</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="main-content">
    <div class="container">
        <?php if (!empty($flashMessage)): ?>
            <div class="alert alert-<?= htmlspecialchars($flashType ?? 'info', ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($flashMessage, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>
