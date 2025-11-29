<?php 

namespace App;

use App\Core\Router;
use App\Core\SessionManager;




// 1. Autoloader
spl_autoload_register(function ($class){
        $namespaceArray = [
                            "namespace"=> ["App\\Controller\\", "App\\Core\\", "App\\Model\\", "App\\Service\\"],
                            "path"=> ["Controllers/", "Core/", "Models/", "Services/"],
                        ];
        $filname = str_ireplace($namespaceArray['namespace'],$namespaceArray['path'], $class  ). ".php";
        $fullPath = __DIR__ . '/' . $filname; 
        if(file_exists($fullPath)) {
            include $fullPath;
        }
    }
);

// 2. Démarrage de la session sécurisée
SessionManager::start();

// 3. Routage de la requête
try {
    $router = new Router('routes.yml'); 
    $router->dispatch();
} catch (\Exception $e) {
    http_response_code(500 );
    echo "<h1>Erreur interne du serveur</h1><p>" . $e->getMessage() . "</p>";
}
