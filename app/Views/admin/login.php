<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion opérateur</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
<div class="login-wrap">
    <div class="login-box">
        <div class="logo">Espace opérateur</div>
        <h1 class="h3">Connexion</h1>
        <p class="text-muted">Connectez-vous pour administrer le système.</p>

        <?php if (session()->getFlashdata('erreur')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('erreur')) ?></div>
        <?php endif; ?>

        <form action="<?= base_url('admin/login') ?>" method="post">
            <div class="mb-3 text-start">
                <label for="username" class="form-label">Identifiant</label>
                <input type="text" class="form-control" id="username" name="username" required autofocus>
            </div>
            <div class="mb-3 text-start">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
        </form>

        <p class="text-muted mt-custom">Par défaut : admin / admin123</p>
        <p class="text-muted"><a href="<?= base_url('/') ?>">Espace client</a></p>
    </div>
</div>
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
