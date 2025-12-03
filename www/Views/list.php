<h2>Pages</h2>

<?php if (!empty($flash)): ?>
    <p><?= htmlspecialchars($flash) ?></p>
<?php endif; ?>

<p><a href="create">Créer une page</a></p>

<table>
    <thead><tr><th>ID</th><th>Title</th><th>Slug</th><th>Publié</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach($pages as $p): ?>
        <tr>
            <td><?= $p->getId() ?></td>
            <td><?= htmlspecialchars($p->getTitle()) ?></td>
            <td><?= htmlspecialchars($p->getSlug()) ?></td>
            <td><?= $p->isPublished() ? 'Oui' : 'Non' ?></td>
            <td>
                <a href="edit?id=<?= $p->getId() ?>">Éditer</a>
                <form method="POST" action="delete" style="display:inline">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Core\SessionManager::generateCsrfToken()) ?>">
                    <input type="hidden" name="id" value="<?= $p->getId() ?>">
                    <button type="submit" onclick="return confirm('Supprimer ?')">Supprimer</button>
                </form>
                <?php if ($p->isPublished()): ?>
                    <a href="/<?= htmlspecialchars($p->getSlug()) ?>" target="_blank">Voir</a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
