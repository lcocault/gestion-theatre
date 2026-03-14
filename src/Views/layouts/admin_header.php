<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Administration', ENT_QUOTES, 'UTF-8') ?> – <?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-body">
<div class="admin-layout">
    <nav class="admin-sidebar">
        <div class="admin-brand">
            <a href="/admin"><?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?></a>
            <small>Administration</small>
        </div>
        <ul class="admin-menu">
            <li><a href="/admin">Tableau de bord</a></li>
            <li><a href="/admin/representations">Représentations</a></li>
            <li><a href="/admin/programmations">Programmations</a></li>
            <li><a href="/admin/pieces">Pièces</a></li>
            <li><a href="/admin/troupes">Troupes</a></li>
            <li><a href="/admin/lieux">Lieux</a></li>
        </ul>
        <div class="admin-user">
            Connecté : <strong><?= htmlspecialchars($adminUsername ?? '', ENT_QUOTES, 'UTF-8') ?></strong><br>
            <a href="/admin/logout">Déconnexion</a>
        </div>
    </nav>
    <div class="admin-main">
        <header class="admin-header">
            <h1><?= htmlspecialchars($pageTitle ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
        </header>
        <div class="admin-content">
