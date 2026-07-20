<?= $this->include('templates/header_admin') ?>

<h1 class="h3 mb-4">Tableau de bord</h1>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="stat-box">
            <div class="num"><?= (int) $nbClients ?></div>
            <div class="lbl">Clients enregistrés</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-box">
            <div class="num"><?= number_format($soldeTotal, 0, ',', ' ') ?> Ar</div>
            <div class="lbl">Total détenu par les clients</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h3 class="card-title h5">Navigation rapide</h3>
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <a href="<?= base_url('admin/prefixes') ?>" class="action-btn"><span class="icon">Préfixes</span></a>
            </div>
            <div class="col-6 col-md-3">
                <a href="<?= base_url('admin/baremes') ?>" class="action-btn"><span class="icon">Barèmes de frais</span></a>
            </div>
            <div class="col-6 col-md-3">
                <a href="<?= base_url('admin/gains') ?>" class="action-btn"><span class="icon">Situation des gains</span></a>
            </div>
            <div class="col-6 col-md-3">
                <a href="<?= base_url('admin/comptes') ?>" class="action-btn"><span class="icon">Comptes clients</span></a>
            </div>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>
