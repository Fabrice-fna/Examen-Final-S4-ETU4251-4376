<?php
// Script de mise a jour de la base SQLite existante (writable/database.db)
$pdo = new PDO('sqlite:writable/database.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 0. Ajout des colonnes frais_operateur / frais_telma a transactions (si absentes)
$cols = $pdo->query('PRAGMA table_info(transactions)')->fetchAll(PDO::FETCH_ASSOC);
$colNames = array_column($cols, 'name');
if (! in_array('frais_operateur', $colNames, true)) {
    $pdo->exec("ALTER TABLE transactions ADD COLUMN frais_operateur INTEGER NOT NULL DEFAULT 0");
}
if (! in_array('frais_telma', $colNames, true)) {
    $pdo->exec("ALTER TABLE transactions ADD COLUMN frais_telma INTEGER NOT NULL DEFAULT 0");
}

// 1. Ajout des colonnes operateur / est_operateur_propre a prefixes (si absentes)
$cols = $pdo->query('PRAGMA table_info(prefixes)')->fetchAll(PDO::FETCH_ASSOC);
$colNames = array_column($cols, 'name');
if (! in_array('operateur', $colNames, true)) {
    $pdo->exec("ALTER TABLE prefixes ADD COLUMN operateur TEXT NOT NULL DEFAULT 'Telma'");
}
if (! in_array('est_operateur_propre', $colNames, true)) {
    $pdo->exec("ALTER TABLE prefixes ADD COLUMN est_operateur_propre INTEGER NOT NULL DEFAULT 1");
}

// 2. Ajout de la colonne nom_utilisateur a clients (si absente)
$cols = $pdo->query('PRAGMA table_info(clients)')->fetchAll(PDO::FETCH_ASSOC);
$colNames = array_column($cols, 'name');
if (! in_array('nom_utilisateur', $colNames, true)) {
    $pdo->exec("ALTER TABLE clients ADD COLUMN nom_utilisateur TEXT NOT NULL DEFAULT ''");
}

// 3. Creation de la table parametres (si absente)
$pdo->exec("CREATE TABLE IF NOT EXISTS parametres (
    cle       TEXT PRIMARY KEY,
    valeur    TEXT NOT NULL,
    libelle   TEXT NOT NULL
)");

// 4. Les parametres (pourcentages de commission) et les prefixes ne sont
// plus inseres directement en SQL ici : ils doivent etre ajoutes depuis
// l'application (admin/commission-propre, admin/commission, admin/prefixes).

// 5. Recreation des vues (DROP IF EXISTS puis CREATE)
$sql = file_get_contents('base.sql');
// Extraire uniquement les definitions de vues
preg_match_all('/DROP VIEW IF EXISTS (\w+);\s*CREATE VIEW \1 AS\s*SELECT.*?;/is', $sql, $matches, PREG_SET_ORDER);
foreach ($matches as $m) {
    $pdo->exec($m[0]);
}

echo "Mise a jour terminee avec succes.\n";
