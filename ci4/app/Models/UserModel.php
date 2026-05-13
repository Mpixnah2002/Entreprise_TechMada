<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'employes';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nom', 'email', 'password', 'role', 'departement_id', 'active'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $returnType = 'array';
}
