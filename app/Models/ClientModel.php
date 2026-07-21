<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table            = 'clients';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['telephone', 'nom_utilisateur', 'solde', 'date_creation'];
    protected $useTimestamps    = false;

    public function findByTelephone(string $telephone): ?array
    {
        return $this->where('telephone', $telephone)->first();
    }

    public function crediter(int $clientId, int $montant): void
    {
        $this->db->query(
            'UPDATE clients SET solde = solde + ? WHERE id = ?',
            [$montant, $clientId]
        );
    }

    public function debiter(int $clientId, int $montant): void
    {
        $this->db->query(
            'UPDATE clients SET solde = solde - ? WHERE id = ?',
            [$montant, $clientId]
        );
    }
}