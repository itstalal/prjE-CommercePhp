<?php
session_start();

// Vérifier si l'utilisateur est connecté et a le rôle d'admin
if (!isset($_SESSION['utilisateur']['courriel']) || $_SESSION['utilisateur']['role'] !== 'admin') {
    header('Location: /');
    exit();
}

// Configuration de la base de données
$servername = "localhost";
$username = "Talal123";
$password = "Talal123";
$dbname = "ProjetPhpS4";
$error = "";
$success = "";

try {
    // Connexion à la base de données
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier si l'ID de l'utilisateur est fourni et valide
    if (isset($params['id']) && is_numeric($params['id'])) {
        $id = intval($params['id']);

        // Début de la transaction
        $conn->beginTransaction();

        // Vérifier si l'utilisateur existe
        $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$utilisateur) {
            throw new Exception("Utilisateur non trouvé.");
        }

        // Supprimer les détails des commandes associés
        $stmt = $conn->prepare("DELETE FROM commande_details WHERE commande_id IN (SELECT id FROM commandes WHERE utilisateur_id = :id)");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Supprimer les commandes
        $stmt = $conn->prepare("DELETE FROM commandes WHERE utilisateur_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Supprimer les enregistrements dans la table panier
        $stmt = $conn->prepare("DELETE FROM panier WHERE utilisateur_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Supprimer l'utilisateur
        $stmt = $conn->prepare("DELETE FROM utilisateurs WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Valider la transaction
        $conn->commit();

        // Redirection après succès
        header('Location: /admin-UserManagment');
        exit();
    } else {
        $error = "ID de l'utilisateur non valide ou non fourni.";
    }
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    $error = "Erreur lors de la suppression : " . $e->getMessage();
}
?>
