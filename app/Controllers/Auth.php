<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\PrefixeModel;

class Auth extends BaseController
{
    public function index()
    {
        if ($this->session->get('client_id')) {
            return redirect()->to('/client');
        }

        return view('auth/login');
    }

    public function attempt()
    {
        $telephone = trim((string) $this->request->getPost('telephone'));

        if ($telephone === '') {
            return redirect()->to('/')->with('erreur', 'Veuillez saisir un numéro de téléphone.');
        }

        $prefixeModel = new PrefixeModel();
        if (! $prefixeModel->estValide($telephone)) {
            return redirect()->to('/')->with('erreur', 'Ce préfixe n\'est pas pris en charge par cet opérateur.');
        }

        $clientModel = new ClientModel();
        $client      = $clientModel->findByTelephone($telephone);

        if (! $client) {
            $id     = $clientModel->insert(['telephone' => $telephone, 'solde' => 0], true);
            $client = $clientModel->find($id);
        }

        $this->session->set([
            'client_id'        => $client['id'],
            'client_telephone' => $client['telephone'],
        ]);

        return redirect()->to('/client');
    }

    public function logout()
    {
        $this->session->destroy();

        return redirect()->to('/');
    }
}
