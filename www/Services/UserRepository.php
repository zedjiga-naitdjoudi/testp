<?php

namespace App\Service;

use App\Core\Database;
use App\Model\User;
use PDO;

class UserRepository
{
    private Database $db;
    private string $table = 'users';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByEmail(string $email): ?User
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->query($sql, ['email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function save(User $user): int
    {
        $sql = "INSERT INTO {$this->table} (name, email, password, confirmation_token, is_confirmed) 
                VALUES (:name, :email, :password, :token, :is_confirmed)";
        
        $params = [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'token' => $user->getConfirmationToken(),
            'is_confirmed' => $user->isConfirmed()
        ];

        $this->db->query($sql, $params);
        return $this->db->getPdo()->lastInsertId($this->table . '_id_seq');
    }

    private function hydrate(array $data): User
    {
        $user = new User();
        $user->setId($data['id'])
             ->setName($data['name'] ?? null)
             ->setEmail($data['email'])
             ->setPassword($data['password'])
             ->setIsConfirmed($data['is_confirmed'])
             ->setConfirmationToken($data['confirmation_token'] ?? null)
             ->setResetToken($data['reset_token'] ?? null)
             ->setResetTokenExpiresAt($data['reset_token_expires_at'] ?? null);
        return $user;
    }
}
