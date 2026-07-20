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
    id       INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe  TEXT NOT NULL UNIQUE,
    actif    INTEGER NOT NULL DEFAULT 1
);


DROP TABLE IF EXISTS types_operations;
CREATE TABLE types_operations (
    id       INTEGER PRIMARY KEY AUTOINCREMENT,
    code     TEXT NOT NULL UNIQUE,   
    libelle  TEXT NOT NULL
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
    COUNT(tr.id)          AS nombre_operations,
    COALESCE(SUM(tr.montant), 0) AS total_montant,
    COALESCE(SUM(tr.frais), 0)   AS total_frais
FROM types_operations t
LEFT JOIN transactions tr ON tr.type_operation_id = t.id
GROUP BY t.id, t.code, t.libelle;


DROP VIEW IF EXISTS vue_situation_clients;
CREATE VIEW vue_situation_clients AS
SELECT
    c.id                  AS client_id,
    c.telephone            AS telephone,
    c.solde                 AS solde,
    c.date_creation          AS date_creation,
    (SELECT COUNT(*) FROM transactions t WHERE t.client_id = c.id) AS nombre_operations
FROM clients c;


PRAGMA foreign_keys = ON;


INSERT INTO prefixes (prefixe, actif) VALUES ('033', 1);
INSERT INTO prefixes (prefixe, actif) VALUES ('037', 1);


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


INSERT INTO clients (telephone, solde) VALUES ('0331234567', 50000);
INSERT INTO clients (telephone, solde) VALUES ('0372345678', 20000);
