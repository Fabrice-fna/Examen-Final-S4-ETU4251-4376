<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - MobileMoney</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
<div class="login-wrap">
    <div class="login-box">
        <div class="logo">MobileMoney</div>
        <h1 class="h3">Connexion</h1>
        <p class="text-muted">Saisissez votre numéro de téléphone pour accéder à votre compte.</p>

        <?php if (session()->getFlashdata('erreur')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('erreur')) ?></div>
        <?php endif; ?>

        <form action="<?= base_url('login') ?>" method="post">
            <div class="mb-3 text-start">
                <label for="telephone" class="form-label">Numéro de téléphone</label>
                <input type="text" class="form-control" id="telephone" name="telephone" placeholder="ex: 0331234567" required autofocus>
            </div>
            <button type="submit" class="btn btn-primary w-100">Continuer</button>
        </form>

        <p class="text-muted mt-custom">Aucune inscription requise. La connexion est automatique.</p>
        <p class="text-muted"><a href="<?= base_url('admin') ?>">Espace opérateur</a></p>
    </div>
</div>
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
