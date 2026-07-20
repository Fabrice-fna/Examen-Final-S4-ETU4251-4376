<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table            = 'transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'client_id', 'client_dest_id', 'type_operation_id',
        'montant', 'frais', 'frais_operateur', 'frais_telma',
        'solde_apres', 'date_operation',
    ];
    protected $useTimestamps    = false;

    public function historiqueClient(int $clientId): array
    {
        $sql = 'SELECT tr.*, t.code AS type_code, t.libelle AS type_libelle,
                    CASE WHEN tr.client_id = ? THEN c2.telephone ELSE c1.telephone END AS destinataire,
                    CASE WHEN tr.client_id = ? THEN \'sortant\' ELSE \'entrant\' END AS direction
                FROM transactions tr
                JOIN types_operations t ON t.id = tr.type_operation_id
                JOIN clients c1 ON c1.id = tr.client_id
                LEFT JOIN clients c2 ON c2.id = tr.client_dest_id
                WHERE tr.client_id = ? OR tr.client_dest_id = ?
                ORDER BY tr.id DESC';

        return $this->db->query($sql, [$clientId, $clientId, $clientId, $clientId])->getResultArray();
    }
}
