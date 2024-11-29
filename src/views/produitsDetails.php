
<?php

$servername = "localhost";
$username = "Talal123";
$password = "Talal123";
$dbname = "ProjetPhpS4";
$error = "";
$success = "";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($params['id'])) {
        $id = $params['id'];
        $stmt = $conn->prepare("
            SELECT produit.*, produit_images.image_url 
            FROM produit 
            LEFT JOIN produit_images ON produit.id = produit_images.produit_id 
            WHERE produit.id = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $produit = $stmt->fetch(PDO::FETCH_ASSOC);      
        
    }
} catch (PDOException $e) {
    $error = "Erreur : " . $e->getMessage();
}
?>

>
<body class="bg-gray-100">

<?php if (!empty($error)): ?>
    <div class="bg-red-500 text-white p-4 text-center"><?= htmlspecialchars($error); ?></div>
<?php elseif ($produit): ?>
    <div class="container mx-auto px-4 py-10" style="margin-top: 80px;">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2">
                 <!-- Section gauche -->
                 <div class="flex items-center justify-center p-6">
                    <?php if (!empty($produit['image_url'])): ?>
                        <img src="<?= htmlspecialchars($produit['image_url']); ?>" alt="Image de <?= htmlspecialchars($produit['nom']); ?>" class="w-full h-auto max-h-96 object-contain rounded-lg shadow-md">
                    <?php else: ?>
                        <div class="flex items-center justify-center h-96 w-full bg-gray-200 text-gray-500">Pas d'image disponible</div>
                    <?php endif; ?>
                </div>
                <!-- Section droite -->
                <div class="p-6 flex flex-col justify-center">
                    <h1 class="text-4xl font-extrabold text-gray-800 mb-4"><?= htmlspecialchars($produit['nom']); ?></h1>
                    <div class="flex items-center mb-4">
                        <span class="text-2xl text-blue-500 font-bold mr-4">$<?= htmlspecialchars($produit['nouveau_prix']); ?></span>
                        <span class="line-through text-gray-500 text-lg">$<?= htmlspecialchars($produit['ancien_prix']); ?></span>
                    </div>
                    <p class="text-gray-600 mb-6">
                        <?= htmlspecialchars($produit['description'] ?? 'Aucune description disponible.'); ?>
                    </p>
                    <form method="POST" action="<?= $router->generate('AjouterPanier'); ?>" class="mt-4">
                        <input type="hidden" name="produit_id" value="<?= htmlspecialchars($produit['id']); ?>">
                        <input type="hidden" name="quantite" value="1">
                        <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-6 rounded-lg flex items-center justify-center">
                            <i class="bi bi-bag-plus mr-2"></i> Ajouter au panier
                        </button>
                    </form>
                </div>

               
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="container mx-auto px-4 py-10">
        <h1 class="text-center text-3xl text-red-600 font-semibold">Produit introuvable</h1>
    </div>
<?php endif; ?>

</body>

