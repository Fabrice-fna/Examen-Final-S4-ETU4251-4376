<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['client_id', 'message', 'lu', 'date_creation'];
    protected $useTimestamps    = false;

    public function nonLues(int $clientId): array
    {
        return $this->where('client_id', $clientId)
            ->where('lu', 0)
            ->orderBy('date_creation', 'DESC')
            ->findAll();
    }

    public function marquerCommeLues(int $clientId): void
    {
        $this->where('client_id', $clientId)
            ->where('lu', 0)
            ->set('lu', 1)
            ->update();
    }
}
