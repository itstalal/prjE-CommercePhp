<?php
if (!isset($_SESSION['utilisateur']['id'])) {
    header('Location: /login');
    exit();
}

if (isset($_POST['panier_id'])) {
    $panier_id = $_POST['panier_id'];
    $utilisateur_id = $_SESSION['utilisateur']['id'];

    $servername = "localhost";
    $username = "Talal123";
    $password = "Talal123";
    $dbname = "ProjetPhpS4";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("DELETE FROM panier WHERE id = :panier_id AND utilisateur_id = :utilisateur_id");
        $stmt->bindParam(':panier_id', $panier_id);
        $stmt->bindParam(':utilisateur_id', $utilisateur_id);
        $stmt->execute();

        header('Location: /cart');
        exit();
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
} else {
    echo "ID de panier manquant.";
}
?>
