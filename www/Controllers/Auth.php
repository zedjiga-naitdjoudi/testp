<?php

namespace App\Controller;

use App\Core\View;
use App\Core\SessionManager;
use App\Service\AuthService;
use App\Service\UserRepository;

class Auth
{
    private View $view;
    private AuthService $authService;

    public function __construct()
    {
        $this->view = new View();
        $userRepository = new UserRepository();
        $this->authService = new AuthService($userRepository);
        SessionManager::start();
    }

    public function signupForm(): void
    {
        $csrfToken = SessionManager::generateCsrfToken();
        $this->view->render('signup.php', ['csrf_token' => $csrfToken, 'errors' => SessionManager::get('signup_errors')]);
        SessionManager::set('signup_errors', null);
    }

    public function signup(): void
    {
        $errors = [];

        // 1. CRITIQUE: Vérification de la méthode et du jeton CSRF
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !SessionManager::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $errors[] = "Erreur de sécurité: Jeton CSRF invalide.";
            SessionManager::set('signup_errors', $errors);
            header('Location: /signup');
            exit;
        }

        // 2. Validation et Nettoyage des entrées (Sécurité: filter_input)
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['pwd'] ?? '';
        $passwordConfirm = $_POST['pwdConfirm'] ?? '';

        if (empty($name) || strlen($name) < 2) { $errors[] = "Votre prénom doit faire au minimum 2 caractères."; }
        if (!$email) { $errors[] = "Votre email n'est pas correct."; }
        if ($this->authService->userRepository->findByEmail($email)) { $errors[] = "L'email existe déjà en base de données."; }
        if (strlen($password) < 8 || !preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errors[] = "Votre mot de passe doit faire au minimum 8 caractères avec min, maj, chiffres.";
        }
        if ($password !== $passwordConfirm) { $errors[] = "Votre mot de passe de confirmation ne correspond pas."; }

        if (empty($errors)) {
            try {
                $this->authService->registerUser($name, $email, $password);
                header('Location: /login');
                exit;
            } catch (\Exception $e) {
                $errors[] = "Erreur lors de l'inscription: " . $e->getMessage();
            }
        }

        SessionManager::set('signup_errors', $errors);
        header('Location: /signup');
        exit;
    }

    public function loginForm(): void
    {
        $csrfToken = SessionManager::generateCsrfToken();
        $this->view->render('login.php', ['csrf_token' => $csrfToken, 'error' => SessionManager::get('login_error')]);
        SessionManager::set('login_error', null);
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !SessionManager::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            SessionManager::set('login_error', "Erreur de sécurité: Jeton CSRF invalide.");
            header('Location: /login');
            exit;
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (!$email || empty($password)) {
            SessionManager::set('login_error', "Veuillez remplir tous les champs.");
            header('Location: /login');
            exit;
        }

        $user = $this->authService->authenticate($email, $password);

        if ($user) {
            SessionManager::regenerateId();
            SessionManager::set('user_id', $user->getId());
            SessionManager::set('is_logged_in', true);
            
            header('Location: /dashboard');
            exit;
        } else {
            SessionManager::set('login_error', "Identifiants invalides.");
            header('Location: /login');
            exit;
        }
    }

    public function logout(): void
    {
        SessionManager::destroy();
        header('Location: /');
        exit;
    }
}
