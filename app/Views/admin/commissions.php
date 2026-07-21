<?= $this->include('templates/header_admin') ?>

<h1 class="h3 mb-4">Commissions</h1>
<p class="text-muted">Configuration des pourcentages de commission pour les transferts.</p>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title h5">Transferts vers Telma (034/038)</h2>
                <p class="text-muted">Pourcentage de commission appliqué aux transferts vers les numéros appartenant à notre propre opérateur Telma (034/038).</p>
                <form action="<?= base_url('admin/commission-propre/enregistrer') ?>" method="post" class="row g-3 align-items-end">
                    <div class="col-auto">
                        <label for="pourcentage" class="form-label">Commission (%)</label>
                        <input type="number" class="form-control" id="pourcentage" name="pourcentage" min="0" max="100" step="1" value="<?= (int) $commissionPropre ?>" required>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
                <div class="mt-3">
                    <div class="stat-box">
                        <div class="num"><?= (int) $commissionPropre ?> %</div>
                        <div class="lbl">Commission actuelle pour Telma</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title h5">Transferts vers autres opérateurs</h2>
                <p class="text-muted">Pourcentage de commission appliqué aux transferts vers les numéros appartenant à un autre opérateur.</p>
                <form action="<?= base_url('admin/commission/enregistrer') ?>" method="post" class="row g-3 align-items-end">
                    <div class="col-auto">
                        <label for="pourcentage" class="form-label">Commission (%)</label>
                        <input type="number" class="form-control" id="pourcentage" name="pourcentage" min="0" max="100" step="1" value="<?= (int) $commissionAutre ?>" required>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
                <div class="mt-3">
                    <div class="stat-box">
                        <div class="num"><?= (int) $commissionAutre ?> %</div>
                        <div class="lbl">Commission actuelle pour autres opérateurs</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>
