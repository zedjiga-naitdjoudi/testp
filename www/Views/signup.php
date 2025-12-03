<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
</head>
<body>
    <h1>Inscription</h1>

    
   

    <form method="POST" action="/register">
        
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
        
        <label for="name">Nom:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="pwd">Mot de passe:</label>
        <input type="password" id="pwd" name="pwd" required>

        <label for="pwdConfirm">Confirmer mot de passe:</label>
        <input type="password" id="pwdConfirm" name="pwdConfirm" required>
        
        <button type="submit">S'inscrire</button>
    </form>
    <p>Déjà un compte ? <a href="/login">Se connecter</a></p>
</body>
</html>
