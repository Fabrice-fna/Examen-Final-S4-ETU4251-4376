<?php

namespace App\Controllers;

use App\Models\BaremeFraisModel;
use App\Models\ClientModel;
use App\Models\NotificationModel;
use App\Models\TransactionModel;
use App\Models\TypeOperationModel;

class Client extends BaseController
{
    private ClientModel $clientModel;
    private TypeOperationModel $typeModel;
    private BaremeFraisModel $baremeModel;
    private TransactionModel $transactionModel;
    private NotificationModel $notificationModel;

    public function __construct()
    {
        $this->clientModel          = new ClientModel();
        $this->typeModel            = new TypeOperationModel();
        $this->baremeModel          = new BaremeFraisModel();
        $this->transactionModel     = new TransactionModel();
        $this->notificationModel    = new NotificationModel();
    }

    private function clientCourant()
    {
        $id = $this->session->get('client_id');
        if (! $id) {
            return null;
        }

        return $this->clientModel->find($id);
    }

    public function index()
    {
        $client = $this->clientCourant();
        if (! $client) {
            return redirect()->to('/');
        }

        $notifications = $this->notificationModel->nonLues($client['id']);
        $this->notificationModel->marquerCommeLues($client['id']);

        return view('client/index', ['client' => $client, 'notifications' => $notifications]);
    }

    public function depot()
    {
        $client = $this->clientCourant();
        if (! $client) {
            return redirect()->to('/');
        }

        return view('client/depot', ['client' => $client]);
    }

    public function depotValider()
    {
        $client = $this->clientCourant();
        if (! $client) {
            return redirect()->to('/');
        }

        $montant = (int) $this->request->getPost('montant');
        if ($montant <= 0) {
            return redirect()->to('client/depot')->with('erreur', 'Montant invalide.');
        }

        $type = $this->typeModel->findByCode('depot');

        $this->clientModel->crediter($client['id'], $montant);
        $nouveauSolde = $client['solde'] + $montant;

        $this->transactionModel->insert([
            'client_id'         => $client['id'],
            'client_dest_id'    => null,
            'type_operation_id' => $type['id'],
            'montant'           => $montant,
            'frais'             => 0,
            'solde_apres'       => $nouveauSolde,
        ]);

        return redirect()->to('client')->with('succes', 'Dépôt de ' . number_format($montant, 0, ',', ' ') . ' Ar effectué avec succès.');
    }

    public function retrait()
    {
        $client = $this->clientCourant();
        if (! $client) {
            return redirect()->to('/');
        }

        return view('client/retrait', ['client' => $client]);
    }

    public function retraitValider()
    {
        $client = $this->clientCourant();
        if (! $client) {
            return redirect()->to('/');
        }

        $montant = (int) $this->request->getPost('montant');
        if ($montant <= 0) {
            return redirect()->to('client/retrait')->with('erreur', 'Montant invalide.');
        }

        $type  = $this->typeModel->findByCode('retrait');
        $frais = $this->baremeModel->calculerFrais((int) $type['id'], $montant);
        $total = $montant + $frais;

        if ($total > $client['solde']) {
            return redirect()->to('client/retrait')->with('erreur', 'Solde insuffisant (montant + frais de ' . $frais . ' Ar).');
        }

        $this->clientModel->debiter($client['id'], $total);
        $nouveauSolde = $client['solde'] - $total;

        $this->transactionModel->insert([
            'client_id'         => $client['id'],
            'client_dest_id'    => null,
            'type_operation_id' => $type['id'],
            'montant'           => $montant,
            'frais'             => $frais,
            'solde_apres'       => $nouveauSolde,
        ]);

        return redirect()->to('client')->with('succes', 'Retrait de ' . number_format($montant, 0, ',', ' ') . ' Ar effectué (frais : ' . $frais . ' Ar).');
    }

    public function transfert()
    {
        $client = $this->clientCourant();
        if (! $client) {
            return redirect()->to('/');
        }

        return view('client/transfert', ['client' => $client]);
    }

    public function transfertValider()
    {
        $client = $this->clientCourant();
        if (! $client) {
            return redirect()->to('/');
        }

        $telephoneDest = trim((string) $this->request->getPost('telephone_destinataire'));
        $montant       = (int) $this->request->getPost('montant');

        if ($montant <= 0) {
            return redirect()->to('client/transfert')->with('erreur', 'Montant invalide.');
        }

        if ($telephoneDest === $client['telephone']) {
            return redirect()->to('client/transfert')->with('erreur', 'Vous ne pouvez pas vous transférer de l\'argent à vous-même.');
        }

        $destinataire = $this->clientModel->findByTelephone($telephoneDest);
        if (! $destinataire) {
            return redirect()->to('client/transfert')->with('erreur', 'Numéro de destinataire introuvable.');
        }

        $type  = $this->typeModel->findByCode('transfert');
        $frais = $this->baremeModel->calculerFrais((int) $type['id'], $montant);
        $total = $montant + $frais;

        if ($total > $client['solde']) {
            return redirect()->to('client/transfert')->with('erreur', 'Solde insuffisant (montant + frais de ' . $frais . ' Ar).');
        }

        $this->clientModel->debiter($client['id'], $total);
        $soldeEmetteur = $client['solde'] - $total;

        $this->clientModel->crediter($destinataire['id'], $montant);
        $soldeDestinataire = $destinataire['solde'] + $montant;

        $this->transactionModel->insert([
            'client_id'         => $client['id'],
            'client_dest_id'    => $destinataire['id'],
            'type_operation_id' => $type['id'],
            'montant'           => $montant,
            'frais'             => $frais,
            'solde_apres'       => $soldeEmetteur,
        ]);

        $this->transactionModel->insert([
            'client_id'         => $destinataire['id'],
            'client_dest_id'    => $client['id'],
            'type_operation_id' => $type['id'],
            'montant'           => $montant,
            'frais'             => 0,
            'solde_apres'       => $soldeDestinataire,
        ]);

        $this->notificationModel->insert([
            'client_id' => $destinataire['id'],
            'message'   => 'Vous avez reçu ' . number_format($montant, 0, ',', ' ') . ' Ar de ' . $client['telephone'],
        ]);

        return redirect()->to('client')->with('succes', 'Transfert de ' . number_format($montant, 0, ',', ' ') . ' Ar vers ' . $telephoneDest . ' effectué (frais : ' . $frais . ' Ar).');
    }

    public function historique()
    {
        $client = $this->clientCourant();
        if (! $client) {
            return redirect()->to('/');
        }

        $historique = $this->transactionModel->historiqueClient($client['id']);

        return view('client/historique', ['client' => $client, 'historique' => $historique]);
    }
}
