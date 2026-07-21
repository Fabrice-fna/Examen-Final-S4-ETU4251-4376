<?= $this->include('templates/header_admin') ?>

<h1 class="h3 mb-4">Frais de retrait — Transferts vers notre opérateur Telma (034/038)</h1>
<p class="text-muted">Pourcentage de frais de retrait appliqué aux transferts vers les numéros appartenant à notre propre opérateur Telma (034/038), en plus de la commission.</p>

<div class="card mb-4">
    <div class="card-body">
        <form action="<?= base_url('admin/frais-retrait-propre/enregistrer') ?>" method="post" class="row g-3 align-items-end">
            <div class="col-auto">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="activer" name="activer" value="1" <?= $activer ? 'checked' : '' ?>>
                    <label class="form-check-label" for="activer">Appliquer les frais de retrait</label>
                </div>
            </div>
            <div class="col-auto">
                <label for="pourcentage" class="form-label">Pourcentage de frais de retrait (%)</label>
                <input type="number" class="form-control" id="pourcentage" name="pourcentage" min="0" max="100" step="1" value="<?= (int) $fraisRetrait ?>" required>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="stat-box">
            <div class="num"><?= $activer ? (int) $fraisRetrait . ' %' : 'Désactivé' ?></div>
            <div class="lbl">Frais de retrait actuels pour Telma</div>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>
