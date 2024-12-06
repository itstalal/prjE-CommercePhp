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

<body class="bg-gray-100">

<section class="new-collections py-10">
    <img src="assets/images/banner_product.webp" alt="" style="margin-top:100px;">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-extrabold text-center text-gray-800">NOUVELLES COLLECTIONS</h1>
        <hr class="my-6 border-gray-300" />

        <!-- Barre de recherche  -->
        <form method="GET" class="relative flex justify-center mb-8">
            <input type="text" name="search" class="w-full md:w-1/2 p-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Rechercher un produit ou une catégorie..." value="<?= htmlspecialchars($searchTerm); ?>" autocomplete="off" />
            <button type="submit" class="ml-2 bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-5 rounded-lg">Rechercher</button>
            
            <?php if (!empty($suggestions)) : ?>
                <div class="absolute top-full mt-2 w-full md:w-1/2 bg-white shadow-lg rounded-lg z-10">
                    <?php foreach ($suggestions as $suggestion) : ?>
                        <a href="?search=<?= urlencode($suggestion); ?>" class="block px-4 py-2 hover:bg-blue-100"><?= htmlspecialchars($suggestion); ?></a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </form>

        <!-- Section des cartes produits -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php if (!empty($produits)) : ?>
                <?php foreach ($produits as $produit) : ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-transform duration-300">
                        <a href="<?= $router->generate('produitsDetails', ['id'=>htmlspecialchars($produit['id'])]); ?>">
                            <?php if (!empty($produit['image_url'])): ?>
                                <img src="<?= htmlspecialchars($produit['image_url']); ?>" alt="Image de <?= htmlspecialchars($produit['nom']); ?>" class="w-full h-48 object-cover" />
                            <?php else: ?>
                                <div class="flex items-center justify-center h-48 bg-gray-200 text-gray-500">Pas d'image</div>
                            <?php endif; ?>
                            <div class="p-4">
                                <p class="text-lg font-semibold"><?= htmlspecialchars($produit['nom']); ?></p>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-xl text-blue-500 font-bold">$<?= htmlspecialchars($produit['nouveau_prix']); ?></span>
                                    <span class="line-through text-gray-500">$<?= htmlspecialchars($produit['ancien_prix']); ?></span>
                                </div>
                                <form method="POST" action="<?= $router->generate('AjouterPanier'); ?>" class="mt-4">
                                    <input type="hidden" name="produit_id" value="<?= htmlspecialchars($produit['id']); ?>">
                                    <input type="hidden" name="quantite" value="1">
                                    <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg flex items-center justify-center">
                                        <i class="bi bi-bag-plus mr-2"></i> Ajouter au panier
                                    </button>
                                </form>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <h1 class="col-span-full text-center text-2xl text-red-600 font-semibold">Aucun produit trouvé.</h1>
            <?php endif; ?>
        </div>
    </div>
</section>

</body>
