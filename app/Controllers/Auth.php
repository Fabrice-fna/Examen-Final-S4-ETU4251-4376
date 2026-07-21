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
        $nomUtilisateur = trim((string) $this->request->getPost('nom_utilisateur'));
        $telephone      = trim((string) $this->request->getPost('telephone'));

        if ($nomUtilisateur === '') {
            return redirect()->to('/')->with('erreur', 'Veuillez saisir votre nom d\'utilisateur.');
        }

        if ($telephone === '') {
            return redirect()->to('/')->with('erreur', 'Veuillez saisir un numéro de téléphone.');
        }

        $prefixeModel = new PrefixeModel();
        
        // Verifier que le numero commence par 034 ou 038 (Telma)
        $prefixe = $prefixeModel->prefixePourTelephone($telephone);
        if (! $prefixe || (int) $prefixe['est_operateur_propre'] !== 1) {
            return redirect()->to('/')->with('erreur', 'Seuls les numéros Telma (034 ou 038) peuvent se connecter et envoyer de l\'argent.');
        }

        $clientModel = new ClientModel();
        $client      = $clientModel->findByTelephone($telephone);

        if (! $client) {
            $id     = $clientModel->insert([
                'telephone'       => $telephone,
                'nom_utilisateur' => $nomUtilisateur,
                'solde'           => 0,
            ], true);
            $client = $clientModel->find($id);
        } else {
            // Mettre a jour le nom d'utilisateur si changement
            if ($client['nom_utilisateur'] !== $nomUtilisateur) {
                $clientModel->update($client['id'], ['nom_utilisateur' => $nomUtilisateur]);
                $client['nom_utilisateur'] = $nomUtilisateur;
            }
        }

        $this->session->set([
            'client_id'              => $client['id'],
            'client_telephone'       => $client['telephone'],
            'client_nom_utilisateur' => $client['nom_utilisateur'],
        ]);

        return redirect()->to('/client');
    }

    public function logout()
    {
        $this->session->destroy();

        return redirect()->to('/');
    }
}
