-- =====================================================================
-- base.sql
-- Système de simulation d'un opérateur de Mobile Money
-- Base de données : SQLite
-- Contient : création des tables, des vues, et des données de base
-- =====================================================================

-- On desactive temporairement les cles etrangeres pour pouvoir
-- recreer les tables sans erreur, meme si la base existe deja
-- (utile quand on relance ce script pour reinitialiser les donnees).
PRAGMA foreign_keys = OFF;


DROP TABLE IF EXISTS prefixes;
CREATE TABLE prefixes (
    id                    INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe               TEXT NOT NULL UNIQUE,
    actif                 INTEGER NOT NULL DEFAULT 1,
    operateur             TEXT NOT NULL DEFAULT 'Telma',
    est_operateur_propre  INTEGER NOT NULL DEFAULT 1
);


DROP TABLE IF EXISTS types_operations;
CREATE TABLE types_operations (
    id       INTEGER PRIMARY KEY AUTOINCREMENT,
    code     TEXT NOT NULL UNIQUE,
    libelle  TEXT NOT NULL
);


DROP TABLE IF EXISTS parametres;
CREATE TABLE parametres (
    cle       TEXT PRIMARY KEY,
    valeur    TEXT NOT NULL,
    libelle   TEXT NOT NULL
);


DROP TABLE IF EXISTS baremes_frais;
CREATE TABLE baremes_frais (
    id                  INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation_id   INTEGER NOT NULL,
    montant_min         INTEGER NOT NULL,
    montant_max         INTEGER NOT NULL,
    frais               INTEGER NOT NULL,
    FOREIGN KEY (type_operation_id) REFERENCES types_operations(id)
);


DROP TABLE IF EXISTS clients;
CREATE TABLE clients (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    telephone      TEXT NOT NULL UNIQUE,
    nom_utilisateur TEXT NOT NULL DEFAULT '',
    solde          INTEGER NOT NULL DEFAULT 0,
    date_creation  TEXT NOT NULL DEFAULT (datetime('now'))
);


DROP TABLE IF EXISTS transactions;
CREATE TABLE transactions (
    id                  INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id           INTEGER NOT NULL,
    client_dest_id      INTEGER,
    type_operation_id   INTEGER NOT NULL,
    montant             INTEGER NOT NULL,
    frais               INTEGER NOT NULL DEFAULT 0,
    frais_operateur     INTEGER NOT NULL DEFAULT 0,
    frais_telma         INTEGER NOT NULL DEFAULT 0,
    solde_apres         INTEGER NOT NULL,
    date_operation       TEXT NOT NULL DEFAULT (datetime('now')),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (client_dest_id) REFERENCES clients(id),
    FOREIGN KEY (type_operation_id) REFERENCES types_operations(id)
);


DROP TABLE IF EXISTS notifications;
CREATE TABLE notifications (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id     INTEGER NOT NULL,
    message       TEXT NOT NULL,
    lu            INTEGER NOT NULL DEFAULT 0,
    date_creation TEXT NOT NULL DEFAULT (datetime('now')),
    FOREIGN KEY (client_id) REFERENCES clients(id)
);


DROP VIEW IF EXISTS vue_situation_gains;
CREATE VIEW vue_situation_gains AS
SELECT
    t.id                 AS type_operation_id,
    t.code                AS type_operation,
    t.libelle             AS libelle,
    CASE
        WHEN tr.client_dest_id IS NOT NULL
             AND EXISTS (
                 SELECT 1 FROM clients c
                 JOIN prefixes p ON substr(c.telephone, 1, length(p.prefixe)) = p.prefixe
                 WHERE c.id = tr.client_dest_id AND p.est_operateur_propre = 0
             ) THEN 1
        ELSE 0
    END                   AS est_autre_operateur,
    COUNT(tr.id)          AS nombre_operations,
    COALESCE(SUM(tr.montant), 0) AS total_montant,
    COALESCE(SUM(tr.frais), 0)   AS total_frais
FROM types_operations t
LEFT JOIN transactions tr ON tr.type_operation_id = t.id
GROUP BY t.id, t.code, t.libelle, est_autre_operateur;


DROP VIEW IF EXISTS vue_situation_clients;
CREATE VIEW vue_situation_clients AS
SELECT
    c.id                  AS client_id,
    c.telephone            AS telephone,
    c.nom_utilisateur      AS nom_utilisateur,
    c.solde                 AS solde,
    c.date_creation          AS date_creation,
    (SELECT COUNT(*) FROM transactions t WHERE t.client_id = c.id) AS nombre_operations
FROM clients c;


DROP VIEW IF EXISTS vue_montants_par_operateur;
CREATE VIEW vue_montants_par_operateur AS
SELECT
    COALESCE(p.operateur, 'Telma') AS operateur,
    COALESCE(p.est_operateur_propre, 1)       AS est_operateur_propre,
    COUNT(tr.id)                              AS nombre_transferts,
    COALESCE(SUM(tr.montant), 0)              AS total_montant_a_envoyer,
    COALESCE(SUM(tr.frais), 0)                AS total_frais
FROM transactions tr
JOIN types_operations t ON t.id = tr.type_operation_id AND t.code = 'transfert'
JOIN clients c ON c.id = tr.client_dest_id
LEFT JOIN prefixes p ON substr(c.telephone, 1, length(p.prefixe)) = p.prefixe
GROUP BY operateur, est_operateur_propre;


PRAGMA foreign_keys = ON;


INSERT INTO prefixes (prefixe, actif, operateur, est_operateur_propre) VALUES ('034', 1, 'Telma', 1);
INSERT INTO prefixes (prefixe, actif, operateur, est_operateur_propre) VALUES ('038', 1, 'Telma', 1);
INSERT INTO prefixes (prefixe, actif, operateur, est_operateur_propre) VALUES ('032', 1, 'Orange', 0);
INSERT INTO prefixes (prefixe, actif, operateur, est_operateur_propre) VALUES ('033', 1, 'Airtel', 0);
INSERT INTO prefixes (prefixe, actif, operateur, est_operateur_propre) VALUES ('037', 1, 'Blueline', 0);


INSERT INTO parametres (cle, valeur, libelle) VALUES ('commission_operateur_propre', '50', 'Pourcentage de commission pour les transferts vers notre propre opérateur (Telma 034/038) (%)');
INSERT INTO parametres (cle, valeur, libelle) VALUES ('commission_autres_operateurs', '10', 'Pourcentage de commission pour les transferts vers les autres opérateurs (%)');


INSERT INTO types_operations (code, libelle) VALUES ('depot', 'Dépôt');
INSERT INTO types_operations (code, libelle) VALUES ('retrait', 'Retrait');
INSERT INTO types_operations (code, libelle) VALUES ('transfert', 'Transfert');


INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (2, 100, 1000, 50);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (2, 1001, 5000, 50);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (2, 5001, 10000, 100);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (2, 10001, 25000, 200);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (2, 25001, 50000, 400);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (2, 50001, 100000, 800);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (2, 100001, 250000, 1500);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (2, 250001, 500000, 1500);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (2, 500001, 1000000, 2500);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (2, 1000001, 2000000, 3000);


INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (3, 100, 1000, 50);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (3, 1001, 5000, 50);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (3, 5001, 10000, 100);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (3, 10001, 25000, 200);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (3, 25001, 50000, 400);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (3, 50001, 100000, 800);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (3, 100001, 250000, 1500);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (3, 250001, 500000, 1500);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (3, 500001, 1000000, 2500);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (3, 1000001, 2000000, 3000);


INSERT INTO clients (telephone, nom_utilisateur, solde) VALUES ('0341234567', 'Jean', 50000);
INSERT INTO clients (telephone, nom_utilisateur, solde) VALUES ('0382345678', 'Marie', 20000);
