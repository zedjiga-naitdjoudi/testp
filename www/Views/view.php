<?php if (!isset($page)) : ?>
    <h1>Erreur : page introuvable</h1>
    <?php return; ?>
<?php endif; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($page->getTitle()) ?></title>

</head>
<body>

    <h1><?= htmlspecialchars($page->getTitle()) ?></h1>

    <div class="content">
        <?= $page->getContent() ?>
    </div>

</body>
</html>
