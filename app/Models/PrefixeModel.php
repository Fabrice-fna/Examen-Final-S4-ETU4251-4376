<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeModel extends Model
{
    protected $table            = 'prefixes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['prefixe', 'actif', 'operateur', 'est_operateur_propre'];
    protected $useTimestamps    = false;

    public function estValide(string $telephone): bool
    {
        $actifs = $this->where('actif', 1)->findAll();

        foreach ($actifs as $p) {
            if (strpos($telephone, $p['prefixe']) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retourne le préfixe correspondant à un numéro de téléphone (actif).
     */
    public function prefixePourTelephone(string $telephone): ?array
    {
        $actifs = $this->where('actif', 1)->findAll();

        foreach ($actifs as $p) {
            if (strpos($telephone, $p['prefixe']) === 0) {
                return $p;
            }
        }

        return null;
    }

    /**
     * Vrai si le numéro appartient à un autre opérateur (transfert inter-opérateurs).
     */
    public function estAutreOperateur(string $telephone): bool
    {
        $prefixe = $this->prefixePourTelephone($telephone);

        return $prefixe !== null && (int) $prefixe['est_operateur_propre'] === 0;
    }

    /**
     * Vrai si le numéro appartient à notre propre opérateur.
     */
    public function estOperateurPropre(string $telephone): bool
    {
        $prefixe = $this->prefixePourTelephone($telephone);

        return $prefixe !== null && (int) $prefixe['est_operateur_propre'] === 1;
    }

    /**
     * Liste des préfixes appartenant à notre opérateur.
     */
    public function prefixesPropres(): array
    {
        return $this->where('est_operateur_propre', 1)->findAll();
    }

    /**
     * Liste des préfixes appartenant aux autres opérateurs.
     */
    public function prefixesAutresOperateurs(): array
    {
        return $this->where('est_operateur_propre', 0)->findAll();
    }
}
