<?php

namespace App\Controllers;

use App\Models\BaremeFraisModel;
use App\Models\ClientModel;
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

        return view('admin/prefixes', ['prefixes' => $model->findAll()]);
    }

    public function prefixeAjouter()
    {
        if (! $this->estConnecte()) {
            return redirect()->to('admin');
        }

        $prefixe = trim((string) $this->request->getPost('prefixe'));
        if ($prefixe !== '') {
            $model = new PrefixeModel();
            if (! $model->where('prefixe', $prefixe)->first()) {
                $model->insert(['prefixe' => $prefixe, 'actif' => 1]);
            }
        }

        return redirect()->to('admin/prefixes')->with('succes', 'Préfixe ajouté.');
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
        $gains = $db->query('SELECT * FROM vue_situation_gains')->getResultArray();

        return view('admin/gains', ['gains' => $gains]);
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
