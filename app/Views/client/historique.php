<?= $this->include('templates/header_client') ?>

<div class="card">
    <div class="card-body">
        <h2 class="card-title h4">Historique des opérations</h2>

        <?php if (empty($historique)): ?>
            <p class="text-muted">Aucune opération pour le moment.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Montant</th>
                            <th>Frais</th>
                            <th>Détail</th>
                            <th>Solde après</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($historique as $h): ?>
                        <tr>
                            <td><?= esc($h['date_operation']) ?></td>
                            <td><span class="badge badge-<?= esc($h['type_code']) ?>"><?= esc($h['type_libelle']) ?></span></td>
                            <td><?= number_format($h['montant'], 0, ',', ' ') ?> Ar</td>
                            <td><?= number_format($h['frais'], 0, ',', ' ') ?> Ar</td>
                            <td><?= $h['direction'] === 'entrant' ? 'De ' . esc($h['destinataire']) : ($h['destinataire'] ? 'Vers ' . esc($h['destinataire']) : '-') ?></td>
                            <td><?= number_format($h['solde_apres'], 0, ',', ' ') ?> Ar</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <a href="<?= base_url('client') ?>" class="btn btn-secondary mt-3">Retour</a>
    </div>
</div>

<?= $this->include('templates/footer') ?>
