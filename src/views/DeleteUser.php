<?php
if (isset($_SESSION['utilisateur']['courriel']) && $_SESSION['utilisateur']['role'] !== 'admin') {
    header('Location: /');
    exit();
}

$servername = "localhost";
$username = "Talal123";
$password = "Talal123";
$dbname = "projetfins4";
$error = "";
$success = "";

// Connexion a la base de donnees
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recuperer les informations de l'utilisateur
    if (isset($params['id'])) {
        $id = $params['id'];
        $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

         // Suppression de l'utilisateur
        $stmt = $conn->prepare("DELETE FROM utilisateurs  WHERE id = :id");

        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header('Location: /admin-UserManagment');
    }
} catch (PDOException $e) {
    $error = "Erreur : " . $e->getMessage();
}
