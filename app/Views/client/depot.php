<?= $this->include('templates/header_client') ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title h4">Dépôt</h2>
                <p class="text-muted">Solde actuel : <strong><?= number_format($client['solde'], 0, ',', ' ') ?> Ar</strong></p>

                <form action="<?= base_url('client/depot') ?>" method="post">
                    <div class="mb-3">
                        <label for="montant" class="form-label">Montant à déposer (Ar)</label>
                        <input type="number" class="form-control" id="montant" name="montant" min="1" step="1" required autofocus>
                    </div>
                    <p class="text-muted">Le dépôt est automatique et ne comporte aucun frais.</p>
                    <button type="submit" class="btn btn-primary w-100">Valider le dépôt</button>
                    <a href="<?= base_url('client') ?>" class="btn btn-secondary w-100 mt-2">Annuler</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>
