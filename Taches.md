Livraison v1 (Version 1)

ETU4376:
- Mise en place du projet CodeIgniter 4
- Configuration de la base SQLite (`base.sql` : tables, vues, données de base)
- Modèles : ClientModel, PrefixeModel, TypeOperationModel, BaremeFraisModel, TransactionModel
- Espace opérateur : configuration des préfixes valables
- Espace opérateur : création des types d'opérations et barèmes de frais par tranche (modifiable)
- Espace opérateur : situation des gains via les frais (retrait, transfert)
- Espace opérateur : situation des comptes clients

ETU4251 :
- Espace client : login automatique par numéro de téléphone (sans inscription)
- Espace client : consultation du solde
- Espace client : dépôt automatique
- Espace client : retrait automatique (avec calcul des frais selon barème)
- Espace client : transfert vers un autre client (avec calcul des frais)
- Espace client : historique des opérations
- Intégration graphique (CSS local, sans dépendance internet)

## Livraison v2

ETU 4376 : Backend & Logique de Calcul
-En charge de la configuration des règles métiers, des calculs de frais et des API. 
-Gestion des Frais de Retrait (Option "Inclure les frais")Implémenter la logique de calcul pour l'opérateur principal (ex: Orange/Airtel/Telma selon le contexte) où les frais s'appliquent.Configurer l'exception : appliquer $0$ frais de retrait pour tous les autres opérateurs. 
-Logique d'Envoi Multiple (Division du montant)Créer l'algorithme qui prend le montant total saisi par le client et le divise équitablement par le nombre de numéros destinataires.
-Gérer les arrondis de division (ex: que faire s'il reste des centimes/décimales non divisibles). 
-Validation de la Cohérence des OpérateursMettre en place un filtre de vérification stricte : bloquer la requête si les numéros d'un envoi multiple n'appartiennent pas au même opérateur. 
-Développement des Endpoints APICréer l'API de simulation (calcul des montants par numéro + frais avant validation).Créer l'API finale d'exécution de l'envoi multiple.


ETU 4251 : Frontend & Expérience Utilisateur (UI/UX)
-En charge de l'interface client, des validations de saisie et de l'affichage dynamique. -Formulaire d'Envoi MultipleCréer le champ de saisie dynamique permettant d'ajouter plusieurs numéros de téléphone.Intégrer un système de détection automatique ou de sélection de l'opérateur. 
-Contrôles et Validations UIBloquer la saisie ou afficher une erreur si l'utilisateur tente d'ajouter un numéro d'un opérateur différent du premier numéro saisi (Règle : Même opérateur uniquement). 
-Option "Inclure les frais de retrait"Ajouter une case à cocher (Toggle/Checkbox) "Inclure les frais de retrait".
-Rendre cette option visuelle ou active uniquement pour l'opérateur concerné (et masquée/désactivée à $0$ pour les autres). 
-Affichage du Récapitulatif (Avant validation)Concevoir un tableau ou un résumé clair pour le client :Montant total global.
-Montant net par numéro (après division).Frais de retrait par numéro (si option cochée). Connexion aux APILier l'interface aux endpoints créés par ETU 4376 pour afficher les calculs en temps réel.

## Livraison v3

_(à compléter lors de la prochaine partie du sujet)_
