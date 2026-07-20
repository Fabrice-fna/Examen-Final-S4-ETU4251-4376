<?= $this->include('templates/header_admin') ?>

<h1 class="h3 mb-4">Situation des gains</h1>
<p class="text-muted">Gains de l'opérateur générés par les frais des différentes opérations, séparés entre Telma et les autres opérateurs.</p>

<div class="card mb-4">
    <div class="card-body">
        <h3 class="card-title h5">Gains — Telma (034/038)</h3>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Type d'opération</th>
                        <th>Nombre d'opérations</th>
                        <th>Total des montants traités</th>
                        <th>Total des frais perçus</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($gainsPropres as $g): ?>
                    <tr>
                        <td><span class="badge badge-<?= esc($g['type_operation']) ?>"><?= esc($g['libelle']) ?></span></td>
                        <td><?= (int) $g['nombre_operations'] ?></td>
                        <td><?= number_format($g['total_montant'], 0, ',', ' ') ?> Ar</td>
                        <td><?= number_format($g['total_frais'], 0, ',', ' ') ?> Ar</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h3 class="card-title h5">Gains — Autres opérateurs</h3>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Type d'opération</th>
                        <th>Nombre d'opérations</th>
                        <th>Total des montants traités</th>
                        <th>Total des frais perçus</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($gainsAutres)): ?>
                    <tr><td colspan="4" class="text-muted">Aucun transfert vers un autre opérateur pour le moment.</td></tr>
                <?php else: ?>
                    <?php foreach ($gainsAutres as $g): ?>
                        <tr>
                            <td><span class="badge badge-<?= esc($g['type_operation']) ?>"><?= esc($g['libelle']) ?></span></td>
                            <td><?= (int) $g['nombre_operations'] ?></td>
                            <td><?= number_format($g['total_montant'], 0, ',', ' ') ?> Ar</td>
                            <td><?= number_format($g['total_frais'], 0, ',', ' ') ?> Ar</td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="stat-box">
            <div class="num"><?= number_format($totalPropre, 0, ',', ' ') ?> Ar</div>
            <div class="lbl">Gain — Telma</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box">
            <div class="num"><?= number_format($totalAutre, 0, ',', ' ') ?> Ar</div>
            <div class="lbl">Gain — Autres opérateurs</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box">
            <div class="num"><?= number_format($totalFrais, 0, ',', ' ') ?> Ar</div>
            <div class="lbl">Gain total de l'opérateur</div>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>
