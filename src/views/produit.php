<?php
$servername = "localhost";
$username = "Talal123";
$password = "Talal123";

try {
    $conn = new PDO("mysql:host=$servername;dbname=ProjetPhpS4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
    $suggestions = [];
    $produits = [];

    // Suggestions pour l'autocomplétion
    if (!empty($searchTerm)) {
        $stmt = $conn->prepare("
            SELECT DISTINCT nom 
            FROM produit 
            WHERE nom LIKE :searchTerm 
            LIMIT 5
        ");
        $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
        $stmt->execute();
        $suggestions = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Requête principale pour les produits
    $sql = "
        SELECT produit.*, categoryproduits.nom AS categorie, produit_images.image_url
        FROM produit
        LEFT JOIN categoryproduits ON produit.category_id = categoryproduits.id
        LEFT JOIN produit_images ON produit.id = produit_images.produit_id
    ";

    if (!empty($searchTerm)) {
        $sql .= " WHERE produit.nom LIKE :searchTerm OR categoryproduits.nom LIKE :searchTerm";
    }

    $stmt = $conn->prepare($sql);

    if (!empty($searchTerm)) {
        $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
    }

    $stmt->execute();
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<section class="new-collections">
    <h1>NOUVELLES COLLECTIONS</h1>
    <hr />

    <!-- Barre de recherche avec suggestions -->
    <form method="GET" class="search-form" style="position: relative;">
        <input type="text" name="search" placeholder="Rechercher un produit ou une catégorie..." value="<?= htmlspecialchars($searchTerm); ?>" autocomplete="off" />
        <button type="submit" class="btn btn-primary">Rechercher</button>

        <?php if (!empty($suggestions)) : ?>
            <div class="suggestions-box">
                <?php foreach ($suggestions as $suggestion) : ?>
                    <div class="suggestion-item">
                        <a href="?search=<?= urlencode($suggestion); ?>"><?= htmlspecialchars($suggestion); ?></a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </form>

    <!-- section des cartes produits -->
    <div class="product-grid">
        <?php if (!empty($produits)) : ?>
            <?php foreach ($produits as $produit) : ?>
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
