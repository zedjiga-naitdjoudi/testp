<?php

namespace App\Controller;

use App\Core\View;
use App\Core\SessionManager;

class Base
{
    private View $view;

    public function __construct()
    {
        $this->view = new View();
        SessionManager::start();
    }

    public function index(): void
    {
        $data = [
            'title' => 'Accueil',
            'content' => 'Bienvenue sur la page d\'accueil de notre mini-Wordpress.',
            'is_logged_in' => SessionManager::get('is_logged_in')
        ];
        
        $this->view->render('home.php', $data);
    }

    public function dashboard(): void
    {
        if (!SessionManager::get('is_logged_in')) {
            header('Location: /login');
            exit;
        }
        $this->view->render('dashboard.php', ['title' => 'Tableau de Bord']);
    }
}
