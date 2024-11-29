<?php
$conn = new PDO('mysql:host=localhost;dbname=ProjetPhpS4', 'Talal123', 'Talal123');

$order = isset($_GET['order']) && $_GET['order'] === 'low_to_high' ? 'ASC' : 'DESC';

$query = "
    SELECT p.id, p.nom, p.nouveau_prix, p.ancien_prix, p.quantite, pi.image_url 
    FROM produit p
    JOIN categoryproduits c ON p.category_id = c.id
    LEFT JOIN produit_images pi ON p.id = pi.produit_id
    WHERE c.nom = :categoryName
    ORDER BY p.nouveau_prix $order
";
$stmt = $conn->prepare($query);
$stmt->execute(['categoryName' => 'Watches']);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<div class="bg-gray-100" style="margin-top: 100px;">
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold text-center mb-8">Produits - Montres</h1>

        <!-- Filtres -->
        <div class="flex justify-right mb-8">
            <select 
                onchange="location = this.value;"
                class="px-4 py-2 border border-gray-300 rounded-lg shadow-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="bi bi-filter"></i>     <option value="?order=default" <?= !isset($_GET['order']) || $_GET['order'] == 'default' ? 'selected' : '' ?>> Trier par prix</option>
                <option value="?order=high_to_low" <?= isset($_GET['order']) && $_GET['order'] == 'high_to_low' ? 'selected' : '' ?>>Haut -> Bas</option>
                <option value="?order=low_to_high" <?= isset($_GET['order']) && $_GET['order'] == 'low_to_high' ? 'selected' : '' ?>>Bas -> Haut</option>
            </select>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($products as $product): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['nom']) ?>" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h2 class="text-xl font-semibold"><?= htmlspecialchars($product['nom']) ?></h2>
                        <p class="text-gray-500">Quantité : <?= htmlspecialchars($product['quantite']) ?></p>
                        <div class="flex items-center justify-between mt-4">
                            <span class="text-green-600 font-bold"><?= htmlspecialchars($product['nouveau_prix']) ?> $</span>
                            <?php if ($product['ancien_prix']): ?>
                                <span class="line-through text-gray-400"><?= htmlspecialchars($product['ancien_prix']) ?> $</span>
                            <?php endif; ?>
                        </div>
                        <form method="POST" action="<?= $router->generate('AjouterPanier'); ?>" style="align-items: center; align-content: center;">
                            <input type="hidden" name="produit_id" value="<?= htmlspecialchars($product['id']); ?>">
                            <input type="hidden" name="quantite" value="1">
                            <button type="submit" class="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                                Ajouter au Panier
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>