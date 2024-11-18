<?php
if (!isset($_SESSION['utilisateur']['courriel']) || $_SESSION['utilisateur']['role'] !== 'admin') {
    header('Location: /');
    exit();
}

$servername = "localhost";
$username = "Talal123";
$password = "Talal123";
$dbname = "ProjetPhpS4";
$error = "";
$success = "";

try {
    // Connect to the database
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if a product ID is provided
    if (isset($params['id'])) {
        $id = $params['id'];

        $conn->beginTransaction();

        $stmt = $conn->prepare("
            SELECT produit_images.image_url 
            FROM produit 
            LEFT JOIN produit_images ON produit.id = produit_images.produit_id 
            WHERE produit.id = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $produit = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($produit) {
            $imagePath = $produit['image_url'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $stmt = $conn->prepare("DELETE FROM produit_images WHERE produit_id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $stmt = $conn->prepare("DELETE FROM produit WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $conn->commit();
 
            header('Location: /admin-ProductManagment');
            exit();
        } else {
            $error = "Produit non trouvé.";
        }
    }
} catch (PDOException $e) {
    $conn->rollBack();
    $error = "Erreur : " . $e->getMessage();
}
?>

