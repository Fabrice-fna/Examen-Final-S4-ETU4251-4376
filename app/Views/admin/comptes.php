<?= $this->include('templates/header_admin') ?>

<h1 class="h3 mb-4">Situation des comptes clients</h1>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Téléphone</th>
                        <th>Solde</th>
                        <th>Nombre d'opérations</th>
                        <th>Date de création</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($comptes as $c): ?>
                    <tr>
                        <td><?= esc($c['telephone']) ?></td>
                        <td><?= number_format($c['solde'], 0, ',', ' ') ?> Ar</td>
                        <td><?= (int) $c['nombre_operations'] ?></td>
                        <td><?= esc($c['date_creation']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>
