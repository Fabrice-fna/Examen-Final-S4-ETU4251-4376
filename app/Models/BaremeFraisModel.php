<?php

namespace App\Models;

use CodeIgniter\Model;

class BaremeFraisModel extends Model
{
    protected $table            = 'baremes_frais';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['type_operation_id', 'montant_min', 'montant_max', 'frais'];
    protected $useTimestamps    = false;

    public function calculerFrais(int $typeOperationId, int $montant): int
    {
        $bareme = $this->where('type_operation_id', $typeOperationId)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->first();

        return $bareme ? (int) $bareme['frais'] : 0;
    }

    public function pourType(int $typeOperationId): array
    {
        return $this->where('type_operation_id', $typeOperationId)
            ->orderBy('montant_min', 'ASC')
            ->findAll();
    }
}
