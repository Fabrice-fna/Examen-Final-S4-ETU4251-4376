<?= $this->include('templates/header_admin') ?>

<h1 class="h3 mb-4">Commission — Transferts vers les autres opérateurs</h1>
<p class="text-muted">Pourcentage de commission supplémentaire appliqué aux transferts vers les numéros appartenant à un autre opérateur (au-delà des frais normaux du barème).</p>

<div class="card mb-4">
    <div class="card-body">
        <form action="<?= base_url('admin/commission/enregistrer') ?>" method="post" class="row g-3 align-items-end">
            <div class="col-auto">
                <label for="pourcentage" class="form-label">Pourcentage supplémentaire (%)</label>
                <input type="number" class="form-control" id="pourcentage" name="pourcentage" min="0" max="100" step="1" value="<?= (int) $commission ?>" required>
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
            <div class="num"><?= (int) $commission ?> %</div>
            <div class="lbl">Commission actuelle</div>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>
