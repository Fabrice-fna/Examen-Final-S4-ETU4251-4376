<?= $this->include('templates/header_client') ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title h4">Transfert</h2>
                <p class="text-muted">Solde actuel : <strong><?= number_format($client['solde'], 0, ',', ' ') ?> Ar</strong></p>

                <form action="<?= base_url('client/transfert') ?>" method="post">
                    <div class="mb-3">
                        <label for="telephone_destinataire" class="form-label">Numéro(s) du (des) destinataire(s)</label>
                        <textarea class="form-control" id="telephone_destinataire" name="telephone_destinataire" rows="3" placeholder="ex: 0341234567 ou plusieurs: 0341234567, 0381234567" required autofocus></textarea>
                        <div class="form-text">Séparez plusieurs numéros par des virgules, points-virgules ou espaces. Le montant saisi sera divisé équitablement entre tous les numéros. <strong>L'envoi à plusieurs numéros n'est possible que vers notre propre opérateur Telma (034/038).</strong></div>
                    </div>
                    <div class="mb-3">
                        <label for="montant" class="form-label">Montant total à transférer (Ar)</label>
                        <input type="number" class="form-control" id="montant" name="montant" min="1" step="1" required>
                        <div class="form-text">Ce montant sera réparti entre les destinataires (ex: 3000 Ar pour 3 numéros = 1000 Ar chacun).</div>
                    </div>
                    <p class="text-muted">
                        <strong>Frais applicables :</strong><br>
                        - Vers Telma (034/038) : <?= (int) $commissionPropre ?>% de commission + frais d'envoi selon le barème<br>
                        - Vers autres opérateurs : <?= (int) $commissionAutre ?>% de commission uniquement (pas de frais d'envoi, pas de frais de retrait)
                    </p>
                    <button type="submit" class="btn btn-primary w-100">Valider le transfert</button>
                    <a href="<?= base_url('client') ?>" class="btn btn-secondary w-100 mt-2">Annuler</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>
