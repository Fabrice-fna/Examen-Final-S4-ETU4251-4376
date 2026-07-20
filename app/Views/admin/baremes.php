<?= $this->include('templates/header_admin') ?>

<h1 class="h3 mb-4">Types d'opérations et barèmes de frais</h1>

<div class="card mb-4">
    <div class="card-body">
        <h3 class="card-title h5">Ajouter une tranche de frais</h3>
        <form action="<?= base_url('admin/baremes/ajouter') ?>" method="post" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="type_operation_id" class="form-label">Type d'opération</label>
                <select class="form-select" id="type_operation_id" name="type_operation_id" required>
                    <?php foreach ($types as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= esc($t['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="montant_min" class="form-label">Montant min</label>
                <input type="number" class="form-control" id="montant_min" name="montant_min" placeholder="Montant min" required>
            </div>
            <div class="col-md-2">
                <label for="montant_max" class="form-label">Montant max</label>
                <input type="number" class="form-control" id="montant_max" name="montant_max" placeholder="Montant max" required>
            </div>
            <div class="col-md-2">
                <label for="frais" class="form-label">Frais</label>
                <input type="number" class="form-control" id="frais" name="frais" placeholder="Frais" required>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Ajouter</button>
            </div>
        </form>
    </div>
</div>

<?php foreach ($types as $t): ?>
    <div class="card mb-4">
        <div class="card-body">
            <h3 class="card-title h5"><?= esc($t['libelle']) ?></h3>
            <?php if (empty($t['baremes'])): ?>
                <p class="text-muted">Aucun barème défini (opération gratuite).</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr><th>Montant min</th><th>Montant max</th><th>Frais</th><th></th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($t['baremes'] as $b): ?>
                            <tr>
                                <td><?= number_format($b['montant_min'], 0, ',', ' ') ?> Ar</td>
                                <td><?= number_format($b['montant_max'], 0, ',', ' ') ?> Ar</td>
                                <td><?= number_format($b['frais'], 0, ',', ' ') ?> Ar</td>
                                <td><a href="<?= base_url('admin/baremes/supprimer/' . $b['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cette tranche ?');">Supprimer</a></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>

<?= $this->include('templates/footer') ?>
