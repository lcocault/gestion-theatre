<?php require VIEW_PATH . '/layouts/admin_login_header.php'; ?>

<div class="login-box">
    <div style="text-align:center;margin-bottom:1.5rem;">
        <strong style="font-family:Georgia,serif;font-size:1.2rem;color:#6B2D8B;"><?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?></strong><br>
        <small style="color:#888;">Administration</small>
    </div>
    <h2 style="text-align:center;margin-bottom:1.25rem;font-size:1.2rem;">Connexion</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="POST" action="/admin/login">
        <?= \App\Middleware\CsrfMiddleware::inputField() ?>

        <div class="form-group">
            <label for="username">Identifiant</label>
            <input type="text" id="username" name="username" required
                   autocomplete="username">
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required
                   autocomplete="current-password">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary" style="width:100%;">Se connecter</button>
        </div>
    </form>
    <div style="text-align:center;margin-top:1rem;">
        <a href="/" style="font-size:.875rem;color:#888;">← Retour au site</a>
    </div>
</div>

<?php require VIEW_PATH . '/layouts/admin_login_footer.php'; ?>
