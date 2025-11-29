<?php

namespace App\Controller;

use App\Core\Render;
use App\Core\SessionManager;
use App\Service\AuthService;
use App\Service\UserRepository;

class Auth
{
    private AuthService $authService;
    private UserRepository $repo;

    public function __construct()
{
    $this->repo = new UserRepository();

    $mailer = new MailerService();
    
    $this->authService = new AuthService($this->repo, $mailer);
    
    SessionManager::start();
}


    public function signupForm(): void
    {
        $csrfToken = SessionManager::generateCsrfToken();

        $render = new Render("signup");
        $render->assign("csrf_token", $csrfToken);
        $render->assign("errors", SessionManager::get("signup_errors"));
        SessionManager::set("signup_errors", null);

        $render->render();
    }
    public function signup(): void
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' ||
            !SessionManager::verifyCsrfToken($_POST['csrf_token'] ?? '')
        ) {
            SessionManager::set("signup_errors", ["Erreur CSRF."]);
            header("Location: /signup");
            exit;
        }

        if (
            !isset($_POST['name'], $_POST['email'], $_POST['pwd'], $_POST['pwdConfirm']) ||
            empty($_POST['name']) ||
            empty($_POST['email']) ||
            empty($_POST['pwd']) ||
            empty($_POST['pwdConfirm'])
        ) {
            SessionManager::set("signup_errors", ["Champs manquants ou tentative XSS."]);
            header("Location: /signup");
            exit;
        }

        // Nettoyage
        $name = ucwords(strtolower(trim($_POST['name'])));
        $email = strtolower(trim($_POST['email']));
        $password = $_POST["pwd"];
        $passwordConfirm = $_POST["pwdConfirm"];

        // Vérifications
        if (strlen($name) < 2) {
            $errors[] = "Votre prénom doit faire au minimum 2 caractères.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email incorrect.";
        } elseif ($this->repo->emailExists($email)) {
            $errors[] = "Email déjà utilisé.";
        }

        if (
            strlen($password) < 8 ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[0-9]/', $password)
        ) {
            $errors[] = "Le mot de passe doit faire 8 caractères avec minuscule, majuscule et chiffre.";
        }

        if ($password !== $passwordConfirm) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }

        if (!empty($errors)) {
            SessionManager::set("signup_errors", $errors);
            header("Location: /signup");
            exit;
        }

        try {
            $this->authService->registerUser($name, $email, $password);
            header("Location: /login");
            exit;
        } catch (\Exception $e) {
            SessionManager::set("signup_errors", ["Erreur interne : " . $e->getMessage()]);
            header("Location: /signup");
            exit;
        }
    }
    
    public function loginForm(): void
    {
        $csrfToken = SessionManager::generateCsrfToken();

        $render = new Render("login");
        $render->assign("csrf_token", $csrfToken);
        $render->assign("error", SessionManager::get("login_error"));
        SessionManager::set("login_error", null);
        $render->render();}

  
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' ||
            !SessionManager::verifyCsrfToken($_POST['csrf_token'] ?? '')
        ) {
            SessionManager::set("login_error", "Erreur CSRF.");
            header("Location: /login");
            exit;}

        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if (!$email || !$password || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            SessionManager::set("login_error", "Veuillez remplir tous les champs.");
            header("Location: /login");
            exit;
        }

        $user = $this->authService->authenticate($email, $password);

        if (!$user) {
            SessionManager::set("login_error", "Identifiants incorrects.");
            header("Location: /login");
            exit;
        }

        SessionManager::regenerateId();
        SessionManager::set("user_id", $user->getId());
        SessionManager::set("is_logged_in", true);

        header("Location: /dashboard");
        exit;}

    public function logout(): void
    {
        SessionManager::destroy();
        header("Location: /");
        exit;}
}
