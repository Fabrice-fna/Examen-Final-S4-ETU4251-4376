<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeModel extends Model
{
    protected $table            = 'prefixes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['prefixe', 'actif'];
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
}
