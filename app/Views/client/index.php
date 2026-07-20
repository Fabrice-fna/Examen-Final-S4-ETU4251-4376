<?= $this->include('templates/header_client') ?>

<?php if (! empty($notifications)): ?>
    <?php foreach ($notifications as $n): ?>
        <div class="alert alert-success"><?= esc($n['message']) ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="solde-card">
            <div class="label">Solde disponible</div>
            <div class="montant"><?= number_format($client['solde'], 0, ',', ' ') ?> Ar</div>
            <div class="label" style="margin-top:8px;">Numéro : <?= esc($client['telephone']) ?></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <a href="<?= base_url('client/depot') ?>" class="action-btn">
            <span class="icon">Dépôt</span>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="<?= base_url('client/retrait') ?>" class="action-btn">
            <span class="icon">Retrait</span>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="<?= base_url('client/transfert') ?>" class="action-btn">
            <span class="icon">Transfert</span>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="<?= base_url('client/historique') ?>" class="action-btn">
            <span class="icon">Historique</span>
        </a>
    </div>
</div>

<?= $this->include('templates/footer') ?>
