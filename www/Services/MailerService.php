<?php

namespace App\Service;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';




class MailerService{

    private array $config ;
    public function __construct(array $config =[]){
            

    //avec mailpit 
        $this->config = $config + [
            'host' => 'mailpit',        // Serveur SMTP par défaut
            'port' => 1025,             // Port SMTP
            'from' => 'no-reply@example.com', // Email d'expéditeur par défaut
            'name' => 'MonSite'         // Nom affiché d’expéditeur
        ];

    }

    //fct pour la config mail 
    private function makeMail(): PHPMailer{
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $this->config['host'];
        $mail->SMTPAuth = false;
        $mail->Port = $this->config['port'];
        $mail->setFrom($this->config['from'], $this->config['name']);


        return $mail;
    }
    //fct pour envoi du mail activation
    public function sendActivation (string $to, string $name, string $activationLink):bool{

        $mail = $this->makeMail();
        try{
            $mail->addAddress($to, $name);
            $mail->isHTML(true); 
            $mail->Body = "Cliquez sur ce lien pour activer votre compte : <a href='{$activationLink}'>Activer</a>";
            $mail->AltBody = $activationLink;
            $mail->send(); 
            return true;
        }
        catch(Exception $e){
            return false;

        }
        
    }
    
    

    
    
    
    
    //fct pour reset le pwd
    public function sendPasswordReset(string $to, string $name, string $resetLink): bool
    {
        $mail = $this->makeMail();
        try {
            $mail->addAddress($to, $name);
            $mail->isHTML(true);
            $mail->Subject = 'Réinitialisation de votre mot de passe';
            $mail->Body = "Cliquez sur ce lien pour réinitialiser : <a href='{$resetLink}'>Réinitialiser</a>";
            $mail->AltBody = $resetLink;
            $mail->send();
            return true;
        } catch (Exception $e) {
            // log error
            return false;
        }
    }

    
}



