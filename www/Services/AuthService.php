<?php

namespace App\Service;

use App\Model\User;

class AuthService
{
    public UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function registerUser(string $name, string $email, string $password): User
    {
        // Sécurité: Hachage du mot de passe (Argon2id)
        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

        try {
            $token = bin2hex(random_bytes(32));
        } catch (\Exception $e) {
            throw new \Exception("Erreur lors de la génération du jeton.");
        }

        $user = new User();
        $user->setName($name)
             ->setEmail($email)
             ->setPassword($hashedPassword)
             ->setConfirmationToken($token)
             ->setIsConfirmed(false);

        $userId = $this->userRepository->save($user);
        $user->setId($userId);
        
        return $user;
    }

    public function authenticate(string $email, string $password): ?User
    {
        $user = $this->userRepository->findByEmail($email);

        if ($user && password_verify($password, $user->getPassword())) {
            // Sécurité: Vérification de re-hachage (à implémenter)
            if (password_needs_rehash($user->getPassword(), PASSWORD_ARGON2ID)) {
                // ...
            }
            return $user;
        }

        return null;
    }
}
