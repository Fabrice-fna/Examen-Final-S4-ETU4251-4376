<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeOperationModel extends Model
{
    protected $table            = 'types_operations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['code', 'libelle'];
    protected $useTimestamps    = false;

    public function findByCode(string $code): ?array
    {
        return $this->where('code', $code)->first();
    }
}
