<?php

namespace App\Core;

class View
{
    private string $viewPath = __DIR__ . '/../Views/';

    public function render(string $template, array $data = []): void
    {
        $file = $this->viewPath . $template;

        if (!file_exists($file)) {
            throw new \Exception("Le fichier de vue '{$template}' est introuvable.");
        }

        extract($data); 

        ob_start();
        require $file;
        $content = ob_get_clean();

        echo $content;
    }
}
