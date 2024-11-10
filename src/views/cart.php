<?php
if (!isset($_SESSION['utilisateur']['id'])) {
    header('Location: /login');
    exit();
}

$servername = "localhost";
$username = "Talal123";
$password = "Talal123";
$dbname = "projetfins4";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $utilisateur_id = $_SESSION['utilisateur']['id'];
    $stmt = $conn->prepare("SELECT panier.id, produit.nom, panier.quantite, produit.nouveau_prix 
                            FROM panier 
                            JOIN produit ON panier.produit_id = produit.id 
                            WHERE panier.utilisateur_id = :utilisateur_id");
    $stmt->bindParam(':utilisateur_id', $utilisateur_id);
    $stmt->execute();
    $panier = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $subtotal = 0;
    foreach ($panier as $element) {
        $subtotal += $element['nouveau_prix'] * $element['quantite'];
    }

    $TPS = $subtotal * 0.05;
    $TVQ = $subtotal * 0.09975;
    $total = $subtotal + $TPS + $TVQ;
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<body class="bg-light">
    <div class="container py-5">
        <h1 class="text-center mb-4">Votre Panier</h1>

        <?php if (empty($panier)): ?>
            <div class="text-center mt-5">
                <p class="text-muted">Votre panier est vide.</p>
                <a href="/" class="btn btn-danger">Retourner à l'accueil</a>
            </div>
        <?php else: ?>
            <div class="row">
                <!-- Left Column -->
                <div class="col-md-4 mb-4">
                    <div class="bg-white p-4 shadow rounded">
                        <h4 class="mb-3">Résumé de la commande</h4>
                        <p>Sous-total : <strong><?php echo number_format($subtotal, 2, ',', ' ') . ' $'; ?></strong></p>
                        <p>TPS (5%) : <strong><?php echo number_format($TPS, 2, ',', ' ') . ' $'; ?></strong></p>
                        <p>TVQ (9.975%) : <strong><?php echo number_format($TVQ, 2, ',', ' ') . ' $'; ?></strong></p>
                        <p class="h5 font-weight-bold">Total : <?php echo number_format($total, 2, ',', ' ') . ' $'; ?></p>
                        <form action="/Confirm-Order" method="POST" class="mt-4">
                            <button type="submit" class="btn btn-primary w-100">Valider la Commande</button>
                        </form>
                        <a href="/produits" class="btn btn-secondary w-100 mt-4">Ajouter d'autres produits</a>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-8">
                    <div class="table-responsive bg-white shadow rounded p-3">
                        <table class="table">
                            <thead class="thead-light">
                                <tr>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Prix Unitaire</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($panier as $element): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($element['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($element['quantite']); ?></td>
                                        <td><?php echo number_format($element['nouveau_prix'], 2, ',', ' ') . ' $'; ?></td>
                                        <td><?php echo number_format($element['nouveau_prix'] * $element['quantite'], 2, ',', ' ') . ' $'; ?></td>
                                        <td>
                                            <form action="<?= $router->generate('DeleteItemCart', ['id' => $element['id']]); ?>" method="POST" class="inline">
                                                <input type="hidden" name="panier_id" value="<?php echo $element['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash3-fill"></i> Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>