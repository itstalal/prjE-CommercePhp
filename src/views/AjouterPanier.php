<?php

// Vérifiez que l'utilisateur est connecté
if (!isset($_SESSION['utilisateur']['id'])) {
    header('Location: /login'); 
    exit();
}

// Récupération des données du formulaire
if (isset($_POST['produit_id']) && isset($_POST['quantite'])) {
    $produit_id = $_POST['produit_id'];
    $quantite = $_POST['quantite'];
    $utilisateur_id = $_SESSION['utilisateur']['id'];

    // Connexion à la base de données
    $servername = "localhost";
    $username = "Talal123";
    $password = "Talal123";
    $dbname = "ProjetPhpS4";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Vérifier si le produit est déjà dans le panier
        $stmt = $conn->prepare("SELECT id, quantite FROM panier WHERE utilisateur_id = :utilisateur_id AND produit_id = :produit_id");
        $stmt->bindParam(':utilisateur_id', $utilisateur_id);
        $stmt->bindParam(':produit_id', $produit_id);
        $stmt->execute();
        $panier_item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($panier_item) {
            // Si le produit est déjà dans le panier, mettre à jour la quantité
            $nouvelle_quantite = $panier_item['quantite'] + $quantite;
            $update_stmt = $conn->prepare("UPDATE panier SET quantite = :quantite WHERE id = :id");
            $update_stmt->bindParam(':quantite', $nouvelle_quantite);
            $update_stmt->bindParam(':id', $panier_item['id']);
            $update_stmt->execute();
        } else {
            // Ajouter un nouveau produit au panier
            $insert_stmt = $conn->prepare("INSERT INTO panier (utilisateur_id, produit_id, quantite) VALUES (:utilisateur_id, :produit_id, :quantite)");
            $insert_stmt->bindParam(':utilisateur_id', $utilisateur_id);
            $insert_stmt->bindParam(':produit_id', $produit_id);
            $insert_stmt->bindParam(':quantite', $quantite);
            $insert_stmt->execute();
        }

        
         header('Location: /cart');
        

    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
} else {
    echo "Données invalides.";
}
?>
