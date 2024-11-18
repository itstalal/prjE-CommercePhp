<?php

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
        throw new Exception("Panier est vide.");
    }

    $subtotal = 0;
    foreach ($panier as $element) {
        $subtotal += $element['nouveau_prix'] * $element['quantite'];
    }

    $TPS = $subtotal * 0.05;
    $TVQ = $subtotal * 0.09975;
    $total = $subtotal + $TPS + $TVQ;

    $stmt = $conn->prepare("INSERT INTO commandes (utilisateur_id, date_commande, total, statut) 
                            VALUES (:utilisateur_id, NOW(), :total, 'en cours')");
    $stmt->bindParam(':utilisateur_id', $utilisateur_id);
    $stmt->bindParam(':total', $total);
    $stmt->execute();
    $commande_id = $conn->lastInsertId();

    foreach ($panier as $element) {
        $stmt = $conn->prepare("INSERT INTO commande_details (commande_id, produit_id, quantite, prix_unitaire) 
                                VALUES (:commande_id, :produit_id, :quantite, :prix_unitaire)");
        $stmt->bindParam(':commande_id', $commande_id);
        $stmt->bindParam(':produit_id', $element['produit_id']);
        $stmt->bindParam(':quantite', $element['quantite']);
        $stmt->bindParam(':prix_unitaire', $element['nouveau_prix']);
        $stmt->execute();
    }

    $stmt = $conn->prepare("DELETE FROM panier WHERE utilisateur_id = :utilisateur_id");
    $stmt->bindParam(':utilisateur_id', $utilisateur_id);
    $stmt->execute();

    echo '<section> valider </section>';
    exit();
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
