<?php

namespace App\Controllers;

use App\Models\BaremeFraisModel;
use App\Models\ClientModel;
use App\Models\NotificationModel;
use App\Models\ParametreModel;
use App\Models\PrefixeModel;
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
            'frais_operateur'   => 0,
            'frais_telma'       => 0,
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
            'frais_operateur'   => 0,
            'frais_telma'       => 0,
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

        $prefixeModel   = new PrefixeModel();
        $parametreModel = new ParametreModel();

        return view('client/transfert', [
            'client'                => $client,
            'commissionPropre'      => $parametreModel->getInt('commission_operateur_propre', 0),
            'commissionAutre'       => $parametreModel->getInt('commission_autres_operateurs', 0),
            'fraisRetraitPropre'    => $parametreModel->getInt('frais_retrait_operateur_propre', 0),
            'fraisTelmaAutre'       => $parametreModel->getInt('frais_telma_autres_operateurs', 50),
        ]);
    }

    /**
     * Calcule les frais d'un transfert.
     * Pour notre operateur (Telma 034/038) : commission % + frais d'envoi (%)
     * Pour les autres operateurs : commission % uniquement (pas de frais d'envoi, pas de frais de retrait)
     * Retourne un tableau avec 'total', 'operateur' et 'telma'.
     */
    private function calculerFraisTransfert(int $montant, bool $operateurPropre): array
    {
        $parametreModel = new ParametreModel();
        $frais          = 0;
        $fraisTelma     = 0;

        if ($operateurPropre) {
            // Commission pour notre operateur (configurable en %)
            $commissionPropre = $parametreModel->getInt('commission_operateur_propre', 0);
            $frais += (int) round($montant * $commissionPropre / 100);

            // Frais de retrait en pourcentage (configurable en admin, defaut 0%)
            // Ne s'applique que si la case est cochee
            $activerFraisRetrait = $parametreModel->get('activer_frais_retrait_operateur_propre', '0') === '1';
            if ($activerFraisRetrait) {
                $fraisRetraitPropre = $parametreModel->getInt('frais_retrait_operateur_propre', 0);
                $frais += (int) round($montant * $fraisRetraitPropre / 100);
            }

            // Pour notre operateur : 100% des frais vont a Telma
            $fraisTelma = $frais;
        } else {
            // Commission pour les autres operateurs (configurable en %)
            $commissionAutre = $parametreModel->getInt('commission_autres_operateurs', 0);
            $frais += (int) round($montant * $commissionAutre / 100);

            // Telma recoit aussi un pourcentage (configurable en admin, defaut 50%)
            $fraisTelmaAutre = $parametreModel->getInt('frais_telma_autres_operateurs', 50);
            $fraisTelma = (int) round($montant * $fraisTelmaAutre / 100);
            $frais += $fraisTelma;
        }

        return [
            'total'     => $frais,
            'operateur' => $frais - $fraisTelma,
            'telma'     => $fraisTelma,
        ];
        $promoActive = $parametreModel ->get('activer_promotion_operateur_propre', '0') === '1';
        if($promoActive){
            $promoPropre = $parametreModel -> getInt('promotion_operateur_propre' /100);
            $frais = (int) round($frais * (100-$promoPropre /100));
            $fraisTelma = $frais
        }

    }

    public function transfertValider()
    {
        $client = $this->clientCourant();
        if (! $client) {
            return redirect()->to('/');
        }

        // Recuperation des numeros (envoi multiple possible, separes par , ; ou espace)
        $numerosBruts = trim((string) $this->request->getPost('telephone_destinataire'));
        $montantTotal = (int) $this->request->getPost('montant');
        $appliquerFrais = $this->request->getPost('appliquer_frais') === '1';

        if ($montantTotal <= 0) {
            return redirect()->to('client/transfert')->with('erreur', 'Montant invalide.');
        }

        $numeros = preg_split('/[\s,;]+/', $numerosBruts, -1, PREG_SPLIT_NO_EMPTY);
        $numeros = array_unique($numeros);

        if (empty($numeros)) {
            return redirect()->to('client/transfert')->with('erreur', 'Veuillez saisir au moins un numéro de destinataire.');
        }

        if (in_array($client['telephone'], $numeros, true)) {
            return redirect()->to('client/transfert')->with('erreur', 'Vous ne pouvez pas vous transférer de l\'argent à vous-même.');
        }

        $prefixeModel = new PrefixeModel();

        // L'envoi multiple (plusieurs numeros) n'est autorise que vers notre
        // propre operateur, et uniquement avec un prefixe propre et valide.
        if (count($numeros) > 1) {
            foreach ($numeros as $num) {
                $prefixe = $prefixeModel->prefixePourTelephone($num);
                if (! $prefixe || (int) $prefixe['est_operateur_propre'] !== 1) {
                    return redirect()->to('client/transfert')->with('erreur',
                        'L\'envoi multiple vers plusieurs numéros n\'est possible que vers notre propre opérateur (numéro invalide : ' . esc($num) . ').');
                }
            }
        }

        // Chargement des destinataires (creation automatique si inexistant)
        $destinataires = [];
        foreach ($numeros as $num) {
            $dest = $this->clientModel->findByTelephone($num);
            if (! $dest) {
                // Creer automatiquement le client destinataire
                $this->clientModel->insert([
                    'telephone'       => $num,
                    'nom_utilisateur' => '',
                    'solde'           => 0,
                ]);
                $dest = $this->clientModel->findByTelephone($num);
            }
            $destinataires[] = $dest;
        }

        // Montant par destinataire (division du montant total)
        $nbDest        = count($destinataires);
        $montantParNum = (int) floor($montantTotal / $nbDest);
        $reste         = $montantTotal - ($montantParNum * $nbDest);

        if ($montantParNum <= 0) {
            return redirect()->to('client/transfert')->with('erreur', 'Le montant par destinataire doit être supérieur à 0.');
        }

        // Calcul des frais par destinataire
        $fraisParDest    = [];
        $totalFrais      = 0;
        $totalFraisOp    = 0;
        $totalFraisTelma = 0;
        foreach ($destinataires as $i => $dest) {
            $operateurPropre = $prefixeModel->estOperateurPropre($dest['telephone']);
            $montant = $montantParNum + ($i === 0 ? $reste : 0); // le reste va au premier
            $frais   = $appliquerFrais ? $this->calculerFraisTransfert($montant, $operateurPropre) : ['total' => 0, 'operateur' => 0, 'telma' => 0];
            $fraisParDest[$i] = $frais;
            $totalFrais      += $frais['total'];
            $totalFraisOp    += $frais['operateur'];
            $totalFraisTelma += $frais['telma'];
        }

        // Pas de frais de retrait pour les autres operateurs
        // (cette option est supprimee car les frais de retrait ne s'appliquent pas aux autres operateurs)

        $totalDebite = $montantTotal + $totalFrais;

        if ($totalDebite > $client['solde']) {
            return redirect()->to('client/transfert')->with('erreur',
                'Solde insuffisant (montant ' . $montantTotal . ' + frais transfert ' . $totalFrais . ' Ar).');
        }

        $type = $this->typeModel->findByCode('transfert');
        $this->clientModel->debiter($client['id'], $totalDebite);
        $soldeEmetteur = $client['solde'] - $totalDebite;

        $this->transactionModel->insert([
            'client_id'         => $client['id'],
            'client_dest_id'    => null,
            'type_operation_id' => $type['id'],
            'montant'           => $montantTotal,
            'frais'             => $totalFrais,
            'frais_operateur'   => $totalFraisOp,
            'frais_telma'       => $totalFraisTelma,
            'solde_apres'       => $soldeEmetteur,
        ]);

        foreach ($destinataires as $i => $dest) {
            $montant = $montantParNum + ($i === 0 ? $reste : 0);
            $this->clientModel->crediter($dest['id'], $montant);
            $soldeDestinataire = $dest['solde'] + $montant;

            $this->transactionModel->insert([
                'client_id'         => $dest['id'],
                'client_dest_id'    => $client['id'],
                'type_operation_id' => $type['id'],
                'montant'           => $montant,
                'frais'             => 0,
                'frais_operateur'   => 0,
                'frais_telma'       => 0,
                'solde_apres'       => $soldeDestinataire,
            ]);

            $this->notificationModel->insert([
                'client_id' => $dest['id'],
                'message'   => 'Vous avez reçu ' . number_format($montant, 0, ',', ' ') . ' Ar de ' . $client['telephone'],
            ]);
        }

        $msg = 'Transfert multiple de ' . number_format($montantTotal, 0, ',', ' ') . ' Ar vers ' . $nbDest .
               ' numéro(s) effectué (frais transfert : ' . $totalFrais . ' Ar, dont ' . $totalFraisOp . ' Ar pour l\'opérateur, ' . $totalFraisTelma . ' Ar pour Telma).';

        return redirect()->to('client')->with('succes', $msg);
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
