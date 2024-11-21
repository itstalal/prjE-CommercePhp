<?php
session_start();

header('Content-Type: application/json');

// Paramètres de connexion à la base de données
$servername = "localhost";
$username = "Talal123";
$password = "Talal123";
$dbname = "ProjetPhpS4";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Décoder les données JSON reçues
    $data = json_decode(file_get_contents('php://input'), true);

    // Vérification des données reçues
    if (!$data || empty($data['utilisateur']) || empty($data['produits']) || empty($data['total'])) {
        echo json_encode(['success' => false, 'message' => 'Données invalides ou manquantes']);
        exit;
    }

    // Démarrer une transaction pour garantir l'intégrité des données
    $conn->beginTransaction();

    // Insérer l'utilisateur
    $stmt = $conn->prepare("
        INSERT INTO utilisateurs (nom, prenom, email) 
        VALUES (:nom, :prenom, :email)
    ");
    $stmt->execute([
        ':nom' => htmlspecialchars($data['utilisateur']['nom']),
        ':prenom' => htmlspecialchars($data['utilisateur']['prenom']),
        ':email' => htmlspecialchars($data['utilisateur']['email'])
    ]);
    $utilisateurId = $conn->lastInsertId();

    // Insérer la commande
    $stmt = $conn->prepare("
        INSERT INTO commandes (utilisateur_id, total) 
        VALUES (:utilisateur_id, :total)
    ");
    $stmt->execute([
        ':utilisateur_id' => $utilisateurId,
        ':total' => floatval($data['total'])
    ]);
    $commandeId = $conn->lastInsertId();

    // Insérer les produits associés à la commande
    $stmt = $conn->prepare("
        INSERT INTO commande_details (commande_id, produit_id, quantite) 
        VALUES (:commande_id, :produit_id, :quantite)
    ");
    foreach ($data['produits'] as $produit) {
        if (empty($produit['id']) || !is_numeric($produit['id'])) {
            throw new Exception("Produit invalide détecté");
        }

        // Ici, on suppose une quantité par défaut de 1 (à ajuster si nécessaire)
        $stmt->execute([
            ':commande_id' => $commandeId,
            ':produit_id' => intval($produit['id']),
            ':quantite' => 1
        ]);
    }

    // Valider la transaction
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Commande enregistrée avec succès']);
} catch (Exception $e) {
    // Annuler la transaction en cas d'erreur
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    // Retourner un message d'erreur
    echo json_encode([
        'success' => false,
        'message' => 'Erreur : ' . $e->getMessage()
    ]);
}
?>
