<?php

namespace App\Controllers;

use App\Models\BaremeFraisModel;
use App\Models\ClientModel;
use App\Models\ParametreModel;
use App\Models\PrefixeModel;
use App\Models\TypeOperationModel;

class Admin extends BaseController
{
    private const ADMIN_USER = 'admin';
    private const ADMIN_PASS = 'admin123';

    private function estConnecte(): bool
    {
        return (bool) $this->session->get('admin_connecte');
    }

    public function login()
    {
        if ($this->estConnecte()) {
            return redirect()->to('admin/dashboard');
        }

        return view('admin/login');
    }

    public function attempt()
    {
        $user = $this->request->getPost('username');
        $pass = $this->request->getPost('password');

        if ($user === self::ADMIN_USER && $pass === self::ADMIN_PASS) {
            $this->session->set('admin_connecte', true);

            return redirect()->to('admin/dashboard');
        }

        return redirect()->to('admin')->with('erreur', 'Identifiants incorrects.');
    }

    public function logout()
    {
        $this->session->remove('admin_connecte');

        return redirect()->to('admin');
    }

    public function dashboard()
    {
        if (! $this->estConnecte()) {
            return redirect()->to('admin');
        }

        $clientModel = new ClientModel();
        $data = [
            'nbClients'    => $clientModel->countAll(),
            'soldeTotal'   => array_sum(array_column($clientModel->findAll(), 'solde')),
        ];

        return view('admin/dashboard', $data);
    }

    public function prefixes()
    {
        if (! $this->estConnecte()) {
            return redirect()->to('admin');
        }

        $model = new PrefixeModel();

        return view('admin/prefixes', [
            'prefixes'   => $model->findAll(),
            'parametres' => (new ParametreModel())->getInt('commission_autres_operateurs', 0),
        ]);
    }

    public function prefixeAjouter()
    {
        if (! $this->estConnecte()) {
            return redirect()->to('admin');
        }

        $prefixe = trim((string) $this->request->getPost('prefixe'));
        $operateur = trim((string) $this->request->getPost('operateur'));
        $estPropre = (int) $this->request->getPost('est_operateur_propre');

        if ($prefixe !== '') {
            $model = new PrefixeModel();
            if (! $model->where('prefixe', $prefixe)->first()) {
                $model->insert([
                    'prefixe'              => $prefixe,
                    'actif'                => 1,
                    'operateur'            => $operateur === '' ? 'Telma' : $operateur,
                    'est_operateur_propre' => $estPropre,
                ]);
            }
        }

        return redirect()->to('admin/prefixes')->with('succes', 'Préfixe ajouté.');
    }

    /**
     * Configuration des commissions (fusionnee).
     */
    public function commissions()
    {
        if (! $this->estConnecte()) {
            return redirect()->to('admin');
        }

        $parametreModel = new ParametreModel();

        return view('admin/commissions', [
            'commissionPropre' => $parametreModel->getInt('commission_operateur_propre', 0),
            'commissionAutre'  => $parametreModel->getInt('commission_autres_operateurs', 0),
        ]);
    }

    /**
     * Configuration du pourcentage de commission pour les transferts
     * vers notre propre opérateur (Telma 034/038).
     */
    public function commissionPropre()
    {
        if (! $this->estConnecte()) {
            return redirect()->to('admin');
        }

        $parametreModel = new ParametreModel();

        return view('admin/commission_propre', [
            'commission' => $parametreModel->getInt('commission_operateur_propre', 0),
        ]);
    }

    public function commissionPropreEnregistrer()
    {
        if (! $this->estConnecte()) {
            return redirect()->to('admin');
        }

        $pourcentage = (int) $this->request->getPost('pourcentage');
        if ($pourcentage < 0) {
            $pourcentage = 0;
        }

        (new ParametreModel())->definir(
            'commission_operateur_propre',
            (string) $pourcentage,
            'Pourcentage de commission pour les transferts vers notre propre opérateur (Telma 034/038) (%)'
        );

        return redirect()->to('admin/commission-propre')->with('succes', 'Commission configurée à ' . $pourcentage . ' %.');
    }

    /**
     * Configuration du pourcentage de commission supplémentaire pour les
     * transferts vers les autres opérateurs.
     */
    public function commission()
    {
        if (! $this->estConnecte()) {
            return redirect()->to('admin');
        }

        $parametreModel = new ParametreModel();

        return view('admin/commission', [
            'commission' => $parametreModel->getInt('commission_autres_operateurs', 0),
        ]);
    }

    public function commissionEnregistrer()
    {
        if (! $this->estConnecte()) {
            return redirect()->to('admin');
        }

        $pourcentage = (int) $this->request->getPost('pourcentage');
        if ($pourcentage < 0) {
            $pourcentage = 0;
        }

        (new ParametreModel())->definir(
            'commission_autres_operateurs',
            (string) $pourcentage,
            'Pourcentage de commission supplémentaire pour les transferts vers les autres opérateurs (%)'
        );

        return redirect()->to('admin/commission')->with('succes', 'Commission configurée à ' . $pourcentage . ' %.');
    }

    /**
     * Configuration du pourcentage de frais de retrait pour les transferts
     * vers notre propre opérateur (Telma 034/038).
     */
    public function fraisRetraitPropre()
    {
        if (! $this->estConnecte()) {
            return redirect()->to('admin');
        }

        $parametreModel = new ParametreModel();

        return view('admin/frais_retrait_propre', [
            'fraisRetrait' => $parametreModel->getInt('frais_retrait_operateur_propre', 0),
            'activer'      => $parametreModel->get('activer_frais_retrait_operateur_propre', '0') === '1',
        ]);
    }

    public function fraisRetraitPropreEnregistrer()
    {
        if (! $this->estConnecte()) {
            return redirect()->to('admin');
        }

        $pourcentage = (int) $this->request->getPost('pourcentage');
        if ($pourcentage < 0) {
            $pourcentage = 0;
        }

        $activer = $this->request->getPost('activer') ? '1' : '0';

        (new ParametreModel())->definir(
            'frais_retrait_operateur_propre',
            (string) $pourcentage,
            'Pourcentage de frais de retrait pour les transferts vers notre propre opérateur (Telma 034/038) (%)'
        );

        (new ParametreModel())->definir(
            'activer_frais_retrait_operateur_propre',
            $activer,
            'Activer les frais de retrait pour les transferts vers notre propre opérateur (1 = oui, 0 = non)'
        );

        return redirect()->to('admin/frais-retrait-propre')->with('succes', 'Frais de retrait configurés à ' . $pourcentage . ' % (activés: ' . ($activer === '1' ? 'oui' : 'non') . ').');
    }

    public function prefixeSupprimer($id)
    {
        if (! $this->estConnecte()) {
            return redirect()->to('admin');
        }

        (new PrefixeModel())->delete((int) $id);

        return redirect()->to('admin/prefixes')->with('succes', 'Préfixe supprimé.');
    }

    public function prefixeToggle($id)
    {
        if (! $this->estConnecte()) {
            return redirect()->to('admin');
        }

        $model  = new PrefixeModel();
        $prefixe = $model->find((int) $id);
        if ($prefixe) {
            $model->update((int) $id, ['actif' => $prefixe['actif'] ? 0 : 1]);
        }

        return redirect()->to('admin/prefixes');
    }

    public function baremes()
    {
        if (! $this->estConnecte()) {
            return redirect()->to('admin');
        }

        $typeModel   = new TypeOperationModel();
        $baremeModel = new BaremeFraisModel();

        $types = $typeModel->findAll();
        foreach ($types as &$t) {
            $t['baremes'] = $baremeModel->pourType((int) $t['id']);
        }

        return view('admin/baremes', ['types' => $types]);
    }

    public function baremeAjouter()
    {
        if (! $this->estConnecte()) {
            return redirect()->to('admin');
        }

        $data = [
            'type_operation_id' => (int) $this->request->getPost('type_operation_id'),
            'montant_min'       => (int) $this->request->getPost('montant_min'),
            'montant_max'       => (int) $this->request->getPost('montant_max'),
            'frais'             => (int) $this->request->getPost('frais'),
        ];

        (new BaremeFraisModel())->insert($data);

        return redirect()->to('admin/baremes')->with('succes', 'Tranche de frais ajoutée.');
    }

    public function baremeSupprimer($id)
    {
        if (! $this->estConnecte()) {
            return redirect()->to('admin');
        }

        (new BaremeFraisModel())->delete((int) $id);

        return redirect()->to('admin/baremes')->with('succes', 'Tranche supprimée.');
    }

    public function gains()
    {
        if (! $this->estConnecte()) {
            return redirect()->to('admin');
        }

        $db = db_connect();
        $gains = $db->query('SELECT * FROM vue_situation_gains ORDER BY type_operation_id, est_autre_operateur')->getResultArray();

        $gainsPropres   = [];
        $gainsAutres    = [];
        $totalPropre    = 0;
        $totalAutre     = 0;

        foreach ($gains as $g) {
            if ((int) $g['est_autre_operateur'] === 1) {
                $gainsAutres[] = $g;
                $totalAutre   += (int) $g['total_frais'];
            } else {
                $gainsPropres[] = $g;
                $totalPropre   += (int) $g['total_frais'];
            }
        }

        return view('admin/gains', [
            'gainsPropres' => $gainsPropres,
            'gainsAutres'  => $gainsAutres,
            'totalPropre'  => $totalPropre,
            'totalAutre'   => $totalAutre,
            'totalFrais'   => $totalPropre + $totalAutre,
        ]);
    }

    /**
     * Détail des frais par transfert, répartis entre opérateur et Telma.
     */
    public function frais()
    {
        if (! $this->estConnecte()) {
            return redirect()->to('admin');
        }

        $db = db_connect();
        $transferts = $db->query('
            SELECT
                tr.id,
                tr.date_operation,
                c1.telephone AS emetteur,
                c2.telephone AS destinataire,
                COALESCE(p.operateur, "Telma") AS operateur,
                COALESCE(p.est_operateur_propre, 1) AS est_operateur_propre,
                tr.montant,
                tr.frais,
                tr.frais_operateur,
                tr.frais_telma
            FROM transactions tr
            JOIN types_operations t ON t.id = tr.type_operation_id AND t.code = "transfert"
            JOIN clients c1 ON c1.id = tr.client_id
            JOIN clients c2 ON c2.id = tr.client_dest_id
            LEFT JOIN prefixes p ON substr(c2.telephone, 1, length(p.prefixe)) = p.prefixe
            ORDER BY tr.id DESC
        ')->getResultArray();

        $totalFrais      = 0;
        $totalFraisOp    = 0;
        $totalFraisTelma = 0;
        foreach ($transferts as $t) {
            $totalFrais      += (int) $t['frais'];
            $totalFraisOp    += (int) $t['frais_operateur'];
            $totalFraisTelma += (int) $t['frais_telma'];
        }

        return view('admin/frais', [
            'transferts'     => $transferts,
            'totalFrais'     => $totalFrais,
            'totalFraisOp'   => $totalFraisOp,
            'totalFraisTelma'=> $totalFraisTelma,
        ]);
    }

    /**
     * Situation des montants à envoyer à chaque opérateur (transferts sortants).
     */
    public function montantsParOperateur()
    {
        if (! $this->estConnecte()) {
            return redirect()->to('admin');
        }

        $db        = db_connect();
        $montants  = $db->query('SELECT * FROM vue_montants_par_operateur ORDER BY est_operateur_propre DESC, operateur')->getResultArray();
        $totalEnvoi = 0;
        foreach ($montants as $m) {
            $totalEnvoi += (int) $m['total_montant_a_envoyer'];
        }

        return view('admin/montants_operateurs', [
            'montants'    => $montants,
            'totalEnvoi'  => $totalEnvoi,
        ]);
    }

    public function comptes()
    {
        if (! $this->estConnecte()) {
            return redirect()->to('admin');
        }

        $db = db_connect();
        $comptes = $db->query('SELECT * FROM vue_situation_clients ORDER BY solde DESC')->getResultArray();

        return view('admin/comptes', ['comptes' => $comptes]);
    }
}
