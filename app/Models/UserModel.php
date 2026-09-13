<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'username', 'email', 'password', 'full_name', 'phone', 'role', 'is_active',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'username'  => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,{id}]',
        'email'     => 'required|valid_email|is_unique[users.email,id,{id}]',
        'full_name' => 'required|min_length[2]|max_length[150]',
        'role'      => 'required|in_list[admin,seller,bidder]',
    ];

    /**
     * Hash and persist a new user's password.
     */
    public function registerUser(array $data): int|false
    {
        $data['password']  = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['role']      = $data['role'] ?? 'bidder';
        $data['is_active'] = 1;

        return $this->insert($data, true) ? $this->getInsertID() : false;
    }

    public function findByLogin(string $login): ?array
    {
        return $this->groupStart()
            ->where('username', $login)
            ->orWhere('email', $login)
            ->groupEnd()
            ->first();
    }
}
