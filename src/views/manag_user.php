<?php
if (isset($_SESSION['utilisateur']['courriel']) && $_SESSION['utilisateur']['role'] !== 'admin') {
    header('Location: /');
    exit;
}

$servername = "localhost";
$username = "Talal123";
$password = "Talal123";
$dbname = "ProjetPhpS4";
$error = "";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT * FROM utilisateurs");
    $stmt->execute();

    $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur : " . $e->getMessage();
}
?>

<!-- HTML pour afficher la liste des utilisateurs -->
<div class="table-responsive" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-3 w-75 mx-auto">
        <h1 class="text-center fs-2 fw-bold">La liste de tous les utilisateurs</h1>
        <a href="<?= $router->generate('admin'); ?>" class="btn btn-primary">
            Administrateur
        </a>
    </div>

    <table class="table table-responsive table-bordered w-75 mx-auto">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Nom</th>
                <th class="text-center">Prénom</th>
                <th class="text-center">Email</th>
                <th class="text-center">Numéro</th>
                <th class="text-center">Rôle</th>
                <th class="text-center">Date de création</th>
                <th class="text-center" colspan="2">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($utilisateurs)) : ?>
                <?php foreach ($utilisateurs as $i => $utilisateur) : ?>
                    <tr>
                        <td class="text-center"><?= $i + 1; ?></td>
                        <td class="text-center"><?= htmlspecialchars($utilisateur['nom']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($utilisateur['prenom']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($utilisateur['courriel']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($utilisateur['numero_telephone']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($utilisateur['role']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($utilisateur['created_at']); ?></td>
                        <td class="text-center">
                            <a class="btn btn-primary" href="<?= $router->generate('article', ['id' => $utilisateur['id']]); ?>"><i class="bi bi-pencil-square"></i> Modifier</a>
                            <?php if (isset($_SESSION['utilisateur']['courriel']) && $_SESSION['utilisateur']['role'] == 'admin') {
                                if ($utilisateur['role'] !== 'admin') { ?>

                                    <a class="btn btn-danger" href="<?= $router->generate('DeleteUser',['id'=>$utilisateur['id']]) ?>"><i class="bi bi-trash3-fill"></i> Supprimer</a>
                            <?php }
                            } ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="8" class="text-center">Aucun utilisateur trouvé.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>