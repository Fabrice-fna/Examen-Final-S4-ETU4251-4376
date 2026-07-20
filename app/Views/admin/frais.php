<?= $this->include('templates/header_admin') ?>

<h1 class="h3 mb-4">Détail des frais par transfert</h1>
<p class="text-muted">Répartition des frais pour chaque transfert, divisée en deux colonnes : opérateur et Telma.</p>

<div class="card mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Émetteur</th>
                        <th>Destinataire</th>
                        <th>Opérateur</th>
                        <th>Montant</th>
                        <th>Frais total</th>
                        <th>Opérateur</th>
                        <th>Telma</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($transferts)): ?>
                    <tr><td colspan="9" class="text-muted">Aucun transfert enregistré.</td></tr>
                <?php else: ?>
                    <?php foreach ($transferts as $t): ?>
                        <tr>
                            <td><?= (int) $t['id'] ?></td>
                            <td><?= esc($t['date_operation']) ?></td>
                            <td><?= esc($t['emetteur']) ?></td>
                            <td><?= esc($t['destinataire']) ?></td>
                            <td>
                                <?php if ((int) $t['est_operateur_propre'] === 1): ?>
                                    <span class="badge badge-depot">Telma (034/038)</span>
                                <?php else: ?>
                                    <span class="badge badge-retrait"><?= esc($t['operateur']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= number_format($t['montant'], 0, ',', ' ') ?> Ar</td>
                            <td><strong><?= number_format($t['frais'], 0, ',', ' ') ?> Ar</strong></td>
                            <td class="text-primary"><?= number_format($t['frais_operateur'], 0, ',', ' ') ?> Ar</td>
                            <td class="text-success"><?= number_format($t['frais_telma'], 0, ',', ' ') ?> Ar</td>
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
            <div class="num"><?= number_format($totalFrais, 0, ',', ' ') ?> Ar</div>
            <div class="lbl">Total des frais</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box">
            <div class="num"><?= number_format($totalFraisOp, 0, ',', ' ') ?> Ar</div>
            <div class="lbl">Total — Opérateur</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box">
            <div class="num"><?= number_format($totalFraisTelma, 0, ',', ' ') ?> Ar</div>
            <div class="lbl">Total — Telma</div>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>
