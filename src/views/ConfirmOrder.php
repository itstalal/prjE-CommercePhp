<?php
session_start();

if (!isset($_SESSION['utilisateur']['id'])) {
    header('Location: /login');
    exit();
}

$servername = "localhost";
$username = "Talal123";
$password = "Talal123";
$dbname = "ProjetPhpS4";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $utilisateur_id = $_SESSION['utilisateur']['id'];

    $stmt = $conn->prepare("SELECT panier.id, produit.id AS produit_id, produit.nom, panier.quantite, produit.nouveau_prix 
                            FROM panier 
                            JOIN produit ON panier.produit_id = produit.id 
                            WHERE panier.utilisateur_id = :utilisateur_id");
    $stmt->bindParam(':utilisateur_id', $utilisateur_id);
    $stmt->execute();
    $panier = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($panier)) {
        throw new Exception("Votre panier est vide.");
    }

    $subtotal = 0;
    foreach ($panier as $element) {
        $subtotal += $element['nouveau_prix'] * $element['quantite'];
    }

    $TPS = $subtotal * 0.05;
    $TVQ = $subtotal * 0.09975;
    $totalGeneral = $subtotal + $TPS + $TVQ;

    // Créez une commande avec le statut 'en cours'
    $stmt = $conn->prepare("INSERT INTO commandes (utilisateur_id, date_commande, total, statut) 
                            VALUES (:utilisateur_id, NOW(), :total, 'en cours')");
    $stmt->bindParam(':utilisateur_id', $utilisateur_id);
    $stmt->bindParam(':total', $totalGeneral);
    $stmt->execute();
    $commande_id = $conn->lastInsertId();

    // Insertion des détails de la commande
    foreach ($panier as $element) {
        $stmt = $conn->prepare("INSERT INTO commande_details (commande_id, produit_id, quantite, prix_unitaire) 
                                VALUES (:commande_id, :produit_id, :quantite, :prix_unitaire)");
        $stmt->bindParam(':commande_id', $commande_id);
        $stmt->bindParam(':produit_id', $element['produit_id']);
        $stmt->bindParam(':quantite', $element['quantite']);
        $stmt->bindParam(':prix_unitaire', $element['nouveau_prix']);
        $stmt->execute();
    }

    // Suppression des produits du panier après création de la commande
    $stmt = $conn->prepare("DELETE FROM panier WHERE utilisateur_id = :utilisateur_id");
    $stmt->bindParam(':utilisateur_id', $utilisateur_id);
    $stmt->execute();

    // Ajout de l'ID de la commande pour utilisation côté frontend
    echo "<input type='hidden' id='commande_id' value='$commande_id'>";

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<!-- Card Design using Tailwind CSS -->
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-xl w-full">
        <h2 class="text-2xl font-semibold text-center mb-6 text-gray-700">Votre commande</h2>
        
        <!-- Cart items -->
        <ul class="space-y-4 mb-6">
            <?php foreach ($panier as $element): ?>
                <li class="flex justify-between items-center bg-gray-50 p-4 rounded-lg shadow-md hover:bg-gray-100 transition duration-300">
                    <span class="font-medium text-gray-800"><?= $element['nom'] ?> x <?= $element['quantite'] ?></span>
                    <span class="text-gray-600"><?= number_format($element['nouveau_prix'], 2, ".", "") ?> €</span>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Totals -->
        <div class="space-y-2 mb-6">
            <div class="flex justify-between font-semibold text-gray-700">
                <span>Sous-total</span>
                <span><?= number_format($subtotal, 2, ".", "") ?> €</span>
            </div>
            <div class="flex justify-between font-semibold text-gray-700">
                <span>TPS (5%)</span>
                <span><?= number_format($TPS, 2, ".", "") ?> €</span>
            </div>
            <div class="flex justify-between font-semibold text-gray-700">
                <span>TVQ (9.975%)</span>
                <span><?= number_format($TVQ, 2, ".", "") ?> €</span>
            </div>
            <div class="flex justify-between font-bold text-lg text-gray-800">
                <span>Total</span>
                <span><?= number_format($totalGeneral, 2, ".", "") ?> €</span>
            </div>
        </div>

        <!-- PayPal Button -->
        <div id="paypal-button-container" class="mt-6 text-center"></div>

        <script src="https://www.paypal.com/sdk/js?client-id=ASHMdqw0mpuwzJ2OgIoa1977jNNiHPl7vtbpZmUbOy5obCV8z6yaDYw1gnP-r4--OXFf9xm9w1aujbSI&components=buttons"></script>
        <script>
            paypal.Buttons({
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: '<?= number_format($totalGeneral, 2, ".", "") ?>'
                            }
                        }]
                    });
                },
                onApprove: async function(data, actions) {
                    const order = await actions.order.capture();
                    console.log(order);

                    // Envoyer l'ID de la commande au serveur
                    const commande_id = document.getElementById('commande_id').value;
                    const response = await fetch('update_order_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ orderID: order.id, commande_id: commande_id })
                    });

                    const result = await response.json();
                    if (result.message) {
                        alert(result.message);
                        window.location.href = "/confirmation_commande.php";
                    } else {
                        alert('Erreur : ' + result.error);
                    }
                },
                onError: function(err) {
                    console.error(err);
                    alert('Une erreur est survenue pendant la transaction.');
                }
            }).render('#paypal-button-container');
        </script>
    </div>
</div>
