<?php
$servername = "localhost";
$username = "Talal123";
$password = "Talal123";

try {
    // Connexion à la base de données
    $conn = new PDO("mysql:host=$servername;dbname=projetfins4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer tous les produits avec leurs images
    $stmt = $conn->query("
        SELECT produit.* , categoryproduits.nom AS categorie, produit_images.image_url
        FROM produit
        LEFT JOIN categoryproduits ON produit.category_id = categoryproduits.id
        LEFT JOIN produit_images ON produit.id = produit_images.produit_id
    ");
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>






<div class="table-responsive" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-3 w-75 mx-auto">
        <h1 class="text-center fs-2 fw-bold">Liste de tous les produits</h1>
        <div>

            <a href="<?= $router->generate('admin'); ?>" class="btn btn-primary">
                Administrateur
            </a>
            <a href="<?= $router->generate('addProduct'); ?>" class="btn btn-success">
                Ajouter un produit
            </a>
        </div>
    </div>

    <table class="table table-bordered table-striped w-75 mx-auto">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Nom</th>
                <th class="text-center">Prix Initial</th>
                <th class="text-center">Prix Réduit</th>
                <th class="text-center">Quantité</th>
                <th class="text-center">Catégorie</th>
                <th class="text-center">Image</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($produits)) : ?>
                <?php foreach ($produits as $i => $produit) : ?>
                    <tr>
                        <td class="text-center"><?= $i + 1; ?></td>
                        <td class="text-center"><?= htmlspecialchars($produit['nom']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($produit['ancien_prix']); ?> €</td>
                        <td class="text-center"><?= htmlspecialchars($produit['nouveau_prix']); ?> €</td>
                        <td class="text-center"><?= htmlspecialchars($produit['quantite']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($produit['categorie']); ?></td>
                        <td class="text-center">
                            <?php if (!empty($produit['image_url'])): ?>
                                <img src="<?= htmlspecialchars($produit['image_url']); ?>" alt="Image de <?= htmlspecialchars($produit['nom']); ?>" class="img-thumbnail" style="width: 50px; height: 50px;">
                            <?php else: ?>
                                Pas d'image
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a class="btn btn-primary" href="<?= $router->generate('UpdateProduct',['id'=>htmlspecialchars($produit['id'])]); ?>"><i class="bi bi-pencil-square"></i> Modifier</a>
                            <a class="btn btn-danger" href="<?= $router->generate('DeleteProduct',['id'=>htmlspecialchars($produit['id'])]); ?>"><i class="bi bi-trash3-fill"></i> Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="8" class="text-center">Aucun produit trouvé.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>