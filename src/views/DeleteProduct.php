<?php
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
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($params['id']) && is_numeric($params['id'])) {
        $id = intval($params['id']); 

        $conn->beginTransaction();

        $stmt = $conn->prepare("SELECT image_url FROM produit_images WHERE produit_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($images as $image) {
            $imagePath = $image['image_url'];
            if (file_exists($imagePath) && !empty($imagePath)) {
                unlink($imagePath);
            }
        }

        $stmt = $conn->prepare("DELETE FROM produit_images WHERE produit_id = :id");
        if (!$stmt->execute([':id' => $id])) {
            throw new Exception("Erreur lors de la suppression des images associées.");
        }

        $stmt = $conn->prepare("DELETE FROM commande_details WHERE produit_id = :id");
        if (!$stmt->execute([':id' => $id])) {
            throw new Exception("Erreur lors de la suppression des détails de commande.");
        }

        $stmt = $conn->prepare("DELETE FROM produit WHERE id = :id");
        if (!$stmt->execute([':id' => $id])) {
            throw new Exception("Erreur lors de la suppression du produit.");
        }

        $conn->commit();

        header('Location: /admin-ProductManagment?success=1');
        exit();
    } else {
        $error = "ID du produit non valide ou non fourni.";
    }
} catch (Exception $e) {
    $conn->rollBack();
    $error = "Erreur : " . $e->getMessage();
}
?>
