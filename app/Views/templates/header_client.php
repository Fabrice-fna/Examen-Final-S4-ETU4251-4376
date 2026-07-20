<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titre ?? 'Mobile Money' ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('client') ?>">MobileMoney</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarClient"
                aria-controls="navbarClient" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarClient">
            <div class="ms-auto">
                <?php if (session()->get('client_id')): ?>
                    <span class="navbar-text me-3"><?= esc(session()->get('client_telephone')) ?></span>
                    <a href="<?= base_url('logout') ?>" class="btn btn-outline-light btn-sm">Déconnexion</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<div class="container py-4">
    <?= $this->include('templates/flash_messages') ?>
