<?php
session_start();
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
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($params['id'])) {
        $id = $params['id'];
        $stmt = $conn->prepare("
            SELECT produit.*, produit_images.image_url 
            FROM produit 
            LEFT JOIN produit_images ON produit.id = produit_images.produit_id 
            WHERE produit.id = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $produit = $stmt->fetch(PDO::FETCH_ASSOC);

        // Update product
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'];
            $ancien_prix = $_POST['ancien_prix'];
            $nouveau_prix = $_POST['nouveau_prix'];
            $quantite = $_POST['quantite'];
            $category_id = $_POST['category_id'];

            $stmt = $conn->prepare("
                UPDATE produit 
                SET nom = :nom, ancien_prix = :ancien_prix, nouveau_prix = :nouveau_prix, 
                    quantite = :quantite, category_id = :category_id 
                WHERE id = :id
            ");
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':ancien_prix', $ancien_prix);
            $stmt->bindParam(':nouveau_prix', $nouveau_prix);
            $stmt->bindParam(':quantite', $quantite);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageTmpName = $_FILES['image']['tmp_name'];
                $imageName = uniqid() . "-" . basename($_FILES['image']['name']);
                $uploadDir = "uploads/";
                $uploadPath = $uploadDir . $imageName;

                if (file_exists($produit['image_url'])) {
                    unlink($produit['image_url']);
                }

                // new image
                if (move_uploaded_file($imageTmpName, $uploadPath)) {
                    $stmt = $conn->prepare("UPDATE produit_images SET image_url = :image_url WHERE produit_id = :id");
                    $stmt->bindParam(':image_url', $uploadPath);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                } else {
                    $error = "Erreur lors de l'upload de l'image.";
                }
            }

            $success = "Produit mis à jour avec succès !";
            
            echo "<script>
    setTimeout(function() {
        window.location.href = '/admin-ProductManagment';
    }, 1000); 
</script>";
        }
    }
} catch (PDOException $e) {
    $error = "Erreur : " . $e->getMessage();
}
?>

<!-- Product Update Form -->
<div class="container mx-auto my-10" style="margin-top: 100px;">
    <div class="max-w-lg mx-auto bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Mettre à jour le produit</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger text-center mb-4"><?= htmlspecialchars($error); ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success text-center mb-4"><?= htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if (!empty($produit)) : ?>
            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <div class="mb-3">
                    <label class="block text-gray-700">Nom du produit:</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($produit['nom']); ?>"
                        class="form-control px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>

                <div class="mb-3">
                    <label class="block text-gray-700">Prix initial:</label>
                    <input type="number" name="ancien_prix" value="<?= htmlspecialchars($produit['ancien_prix']); ?>" step="0.01"
                        class="form-control px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>

                <div class="mb-3">
                    <label class="block text-gray-700">Prix réduit:</label>
                    <input type="number" name="nouveau_prix" value="<?= htmlspecialchars($produit['nouveau_prix']); ?>" step="0.01"
                        class="form-control px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>

                <div class="mb-3">
                    <label class="block text-gray-700">Quantité:</label>
                    <input type="number" name="quantite" value="<?= htmlspecialchars($produit['quantite']); ?>"
                        class="form-control px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>

                <div class="mb-3">
                    <label class="block text-gray-700">Catégorie:</label>
                    <select name="category_id" class="form-control px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                        <?php
                        $query = $conn->query("SELECT id, nom FROM categoryproduits");
                        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                            $selected = $produit['category_id'] == $row['id'] ? 'selected' : '';
                            echo "<option value='" . $row['id'] . "' $selected>" . $row['nom'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="block text-gray-700">Image actuelle:</label>
                    <img width="60px" height="auto" src="<?= htmlspecialchars($produit['image_url']); ?>" alt="Produit Image" class="w-32 h-32 object-cover mb-2">
                    <input type="file" name="image" accept="image/*" class="form-control px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <div class="text-center mt-6">
                    <a href="#" class="btn btn-danger w-5 py-2 font-semibold">Annuler</a>
                    <button type="submit" class="btn btn-primary w-5 py-2 font-semibold">Mettre à jour</button>
                </div>
            </form>
        <?php else : ?>
            <p class="text-center text-red-600">Produit non trouvé.</p>
        <?php endif; ?>
    </div>
</div>