<?php
$servername = "localhost";
$username = "Talal123";
$password = "Talal123";

try {
    $conn = new PDO("mysql:host=$servername;dbname=projetfins4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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







<section class="new-collections" style="margin-top: 100px;">
    <h1>NOUVELLES COLLECTIONS</h1>
    <hr />

    <!-- section des cartes produits -->
    <div class="product-grid">
        <?php if (!empty($produits)) : ?>
            <?php foreach ($produits as $i => $produit) : ?>
                <div class="product-card">
                    <a href="#">
                        <?php if (!empty($produit['image_url'])): ?>
                            <img src="<?= htmlspecialchars($produit['image_url']); ?>" alt="Image de <?= htmlspecialchars($produit['nom']); ?>" class="product-image" />
                        <?php else: ?>
                            <p>Pas d'image</p>
                        <?php endif; ?>

                        <p class="product-name"><?= htmlspecialchars($produit['nom']); ?></p>
                        <div class="item-prices">
                            <div class="item-price-new">$<?= htmlspecialchars($produit['nouveau_prix']); ?></div>
                            <div class="item-price-old">$<?= htmlspecialchars($produit['ancien_prix']); ?></div>
                        </div>

                        <!--  ajout de produit au panier -->
                        <form method="POST" action="<?= $router->generate('AjouterPanier'); ?>">
                            <input type="hidden" name="produit_id" value="<?= htmlspecialchars($produit['id']); ?>">
                            <input type="hidden" name="quantite" value="1">
                            <button type="submit" class="btn btn-warning fs-3">
                                <i class="bi bi-bag-plus"></i>
                            </button>
                        </form>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <h1 class="text-center text-danger">Aucun produit trouvé.</h1>
        <?php endif; ?>
    </div>
</section>