<?= $this->include('templates/header_admin') ?>

<h1 class="h3 mb-4">Montants à envoyer à chaque opérateur</h1>
<p class="text-muted">Situation des montants transférés (transferts sortants) vers chaque opérateur. <strong>Il n'y a pas de frais de retrait pour les autres opérateurs.</strong></p>

<div class="card mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Opérateur</th>
                        <th>Type</th>
                        <th>Nombre de transferts</th>
                        <th>Montant total à envoyer</th>
                        <th>Total des frais</th>
                        <th>Détail des frais</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($montants)): ?>
                    <tr><td colspan="6" class="text-muted">Aucun transfert enregistré.</td></tr>
                <?php else: ?>
                    <?php foreach ($montants as $m): ?>
                        <tr>
                            <td><?= esc($m['operateur']) ?></td>
                            <td>
                                <?php if ((int) $m['est_operateur_propre'] === 1): ?>
                                    <span class="badge badge-depot">Telma (034/038)</span>
                                <?php else: ?>
                                    <span class="badge badge-retrait">Autre opérateur</span>
                                <?php endif; ?>
                            </td>
                            <td><?= (int) $m['nombre_transferts'] ?></td>
                            <td><?= number_format($m['total_montant_a_envoyer'], 0, ',', ' ') ?> Ar</td>
                            <td><?= number_format($m['total_frais'], 0, ',', ' ') ?> Ar</td>
                            <td>
                                <?php if ((int) $m['est_operateur_propre'] === 1): ?>
                                    Commission + frais d'envoi
                                <?php else: ?>
                                    Commission uniquement (pas de frais de retrait)
                                <?php endif; ?>
                            </td>
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
            <div class="num"><?= number_format($totalEnvoi, 0, ',', ' ') ?> Ar</div>
            <div class="lbl">Total des montants à envoyer</div>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>
