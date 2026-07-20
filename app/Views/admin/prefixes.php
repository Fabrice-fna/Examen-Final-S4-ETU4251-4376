<?= $this->include('templates/header_admin') ?>

<h1 class="h3 mb-4">Configuration des préfixes</h1>

<div class="card mb-4">
    <div class="card-body">
        <h3 class="card-title h5">Ajouter un préfixe</h3>
        <form action="<?= base_url('admin/prefixes/ajouter') ?>" method="post" class="row g-3 align-items-end">
            <div class="col-auto">
                <label for="prefixe" class="form-label">Préfixe</label>
                <input type="text" class="form-control" id="prefixe" name="prefixe" placeholder="ex: 038" required>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h3 class="card-title h5">Préfixes valables</h3>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr><th>Préfixe</th><th>Statut</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach ($prefixes as $p): ?>
                    <tr>
                        <td><?= esc($p['prefixe']) ?></td>
                        <td>
                            <?php if ($p['actif']): ?>
                                <span class="badge badge-depot">Actif</span>
                            <?php else: ?>
                                <span class="badge badge-retrait">Inactif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= base_url('admin/prefixes/toggle/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary">Activer/Désactiver</a>
                            <a href="<?= base_url('admin/prefixes/supprimer/' . $p['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ce préfixe ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>
