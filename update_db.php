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

// 4. Seed / mise a jour des parametres
$pdo->exec("INSERT OR IGNORE INTO parametres (cle, valeur, libelle) VALUES ('commission_operateur_propre', '50', 'Pourcentage de commission pour les transferts vers notre propre opérateur (Telma 034/038) (%)')");
$pdo->exec("INSERT OR IGNORE INTO parametres (cle, valeur, libelle) VALUES ('commission_autres_operateurs', '10', 'Pourcentage de commission pour les transferts vers les autres opérateurs (%)')");

// 5. Mise a jour des prefixes : 034 et 038 = Telma (notre operateur)
$pdo->exec("UPDATE prefixes SET operateur = 'Telma', est_operateur_propre = 1 WHERE prefixe IN ('034','038')");
$pdo->exec("UPDATE prefixes SET operateur = 'Orange', est_operateur_propre = 0 WHERE prefixe = '031'");
$pdo->exec("UPDATE prefixes SET operateur = 'Telma', est_operateur_propre = 0 WHERE prefixe = '032'");
$pdo->exec("UPDATE prefixes SET operateur = 'Airtel', est_operateur_propre = 0 WHERE prefixe = '033'");
$pdo->exec("UPDATE prefixes SET operateur = 'Blueline', est_operateur_propre = 0 WHERE prefixe = '037'");

// 6. Recreation des vues (DROP IF EXISTS puis CREATE)
$sql = file_get_contents('base.sql');
// Extraire uniquement les definitions de vues
preg_match_all('/DROP VIEW IF EXISTS (\w+);\s*CREATE VIEW \1 AS\s*SELECT.*?;/is', $sql, $matches, PREG_SET_ORDER);
foreach ($matches as $m) {
    $pdo->exec($m[0]);
}

echo "Mise a jour terminee avec succes.\n";
