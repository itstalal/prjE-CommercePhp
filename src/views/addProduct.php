<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servername = "localhost";
    $username = "Talal123";
    $password = "Talal123";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=projetfins4", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $nom = htmlspecialchars($_POST['nom']);
        $ancien_prix = floatval($_POST['ancien_prix']);
        $nouveau_prix = floatval($_POST['nouveau_prix']);
        $quantite = intval($_POST['quantite']);
        $category_id = intval($_POST['category_id']);

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageTmpName = $_FILES['image']['tmp_name'];
            $imageName = uniqid() . "-" . basename($_FILES['image']['name']);
            $uploadDir = "uploads/"; 
            $uploadPath = $uploadDir . $imageName;

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true); 
            }

            if (move_uploaded_file($imageTmpName, $uploadPath)) {
                $stmt = $conn->prepare("INSERT INTO produit (nom, ancien_prix, nouveau_prix, quantite, category_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nom, $ancien_prix, $nouveau_prix, $quantite, $category_id]);

                $produit_id = $conn->lastInsertId();

                $stmt = $conn->prepare("INSERT INTO produit_images (produit_id, image_url) VALUES (?, ?)");
                $stmt->execute([$produit_id, $uploadPath]);

                header('Location: /admin-ProductManagment');
                exit;
            } else {
                echo "Erreur lors de le telechargement de l'image.";
            }
        } else {
            echo "Veuillez sélectionner une image valide.";
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>







<!-- <h1 class="text-2xl font-bold text-gray-800 mb-8 text-center" style="margin-top:90px;">Ajouter un produit</h1> -->
<section class="mt-32 max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-lg">
    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <div>
            <label for="nom" class="block text-sm font-medium text-gray-700">Nom du produit :</label>
            <input type="text" id="nom" name="nom" required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label for="ancien_prix" class="block text-sm font-medium text-gray-700">Prix initial ($) :</label>
            <input type="number" id="ancien_prix" name="ancien_prix" step="0.01" required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label for="nouveau_prix" class="block text-sm font-medium text-gray-700">Prix réduit ($) :</label>
            <input type="number" id="nouveau_prix" name="nouveau_prix" step="0.01" required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label for="quantite" class="block text-sm font-medium text-gray-700">Quantité :</label>
            <input type="number" id="quantite" name="quantite" required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label for="category" class="block text-sm font-medium text-gray-700">Catégorie :</label>
            <select id="category" name="category_id" required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <?php
                $conn = new PDO("mysql:host=localhost;dbname=projetfins4", "Talal123", "Talal123");
                $query = $conn->query("SELECT id, nom FROM categoryproduits");
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . $row['id'] . "'>" . $row['nom'] . "</option>";
                }
                ?>
            </select>
        </div>

        <div>
            <label for="image" class="block text-sm font-medium text-gray-700">Image :</label>
            <input type="file" id="image" name="image" accept="image/*" required
                class="mt-1 block w-full text-gray-700 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="flex justify-center mt-8">
            <button type="submit" name="add_product"
                class="btn btn-primary">
                Ajouter le produit
            </button>

        </div>
    </form>
</section>