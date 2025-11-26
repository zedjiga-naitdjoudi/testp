<?php

namespace App\Controller;

class ErrorController
{
    public function show(int $code, string $message): void
    {
        echo "<h1>Erreur {$code}</h1>";
        echo "<p>{$message}</p>";
    }
}
