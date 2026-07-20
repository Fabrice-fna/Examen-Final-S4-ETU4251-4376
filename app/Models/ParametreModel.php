<?php

namespace App\Models;

use CodeIgniter\Model;

class ParametreModel extends Model
{
    protected $table            = 'parametres';
    protected $primaryKey       = 'cle';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['cle', 'valeur', 'libelle'];
    protected $useTimestamps    = false;

    /**
     * Retourne la valeur d'un paramètre sous forme de chaîne.
     */
    public function get(string $cle, string $defaut = ''): string
    {
        $row = $this->find($cle);

        return $row ? (string) $row['valeur'] : $defaut;
    }

    /**
     * Retourne la valeur d'un paramètre sous forme d'entier.
     */
    public function getInt(string $cle, int $defaut = 0): int
    {
        return (int) $this->get($cle, (string) $defaut);
    }

    /**
     * Définit (ou met à jour) la valeur d'un paramètre.
     *
     * Note : ne pas nommer cette méthode "set", car CodeIgniter\Model
     * possède déjà une méthode set() (pour le query builder) avec une
     * signature différente ; les redéclarer de façon incompatible
     * provoque une erreur fatale PHP ("must be compatible with").
     */
    public function definir(string $cle, string $valeur, string $libelle = ''): void
    {
        if ($this->find($cle)) {
            $this->update($cle, ['valeur' => $valeur, 'libelle' => $libelle]);
        } else {
            $this->insert(['cle' => $cle, 'valeur' => $valeur, 'libelle' => $libelle]);
        }
    }
}
