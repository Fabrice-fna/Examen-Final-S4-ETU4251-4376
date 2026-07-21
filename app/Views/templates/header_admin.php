<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titre ?? 'Espace opérateur' ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('admin/dashboard') ?>">Espace opérateur</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin"
                aria-controls="navbarAdmin" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarAdmin">
            <div class="ms-auto">
                <a href="<?= base_url('admin/logout') ?>" class="btn btn-outline-light btn-sm">Déconnexion</a>
            </div>
        </div>
    </div>
</nav>
<div class="container py-4">
    <?= $this->include('templates/flash_messages') ?>
    <div class="tabs nav nav-pills mb-4 flex-wrap">
        <a class="nav-link <?= (current_url() == base_url('admin/dashboard') || current_url() == base_url('admin')) ? 'active' : '' ?>" href="<?= base_url('admin/dashboard') ?>">Tableau de bord</a>
        <a class="nav-link <?= strpos(current_url(), 'prefixes') !== false ? 'active' : '' ?>" href="<?= base_url('admin/prefixes') ?>">Préfixes</a>
        <a class="nav-link <?= strpos(current_url(), 'gains') !== false ? 'active' : '' ?>" href="<?= base_url('admin/gains') ?>">Situation des gains</a>
        <a class="nav-link <?= strpos(current_url(), 'frais') !== false && strpos(current_url(), 'frais-retrait') === false ? 'active' : '' ?>" href="<?= base_url('admin/frais') ?>">Détail des frais</a>
        <a class="nav-link <?= strpos(current_url(), 'commissions') !== false ? 'active' : '' ?>" href="<?= base_url('admin/commissions') ?>">Commissions</a>
        <a class="nav-link <?= strpos(current_url(), 'frais-retrait') !== false ? 'active' : '' ?>" href="<?= base_url('admin/frais-retrait-propre') ?>">Frais d'envoi</a>
        <a class="nav-link <?= strpos(current_url(), 'comptes') !== false ? 'active' : '' ?>" href="<?= base_url('admin/comptes') ?>">Comptes clients</a>
    </div>
