<?= $this->include('templates/header_admin') ?>

<h1 class="h3 mb-4">Situation des gains</h1>
<p class="text-muted">Gains de l'opérateur générés par les frais des différentes opérations.</p>

<div class="card mb-4">
    <div class="card-body">
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
                <?php $totalFrais = 0; ?>
                <?php foreach ($gains as $g): ?>
                    <?php $totalFrais += $g['total_frais']; ?>
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

<div class="row g-3">
    <div class="col-md-4">
        <div class="stat-box">
            <div class="num"><?= number_format($totalFrais, 0, ',', ' ') ?> Ar</div>
            <div class="lbl">Gain total de l'opérateur</div>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>
