<?php

$servername = "localhost";
$username = "Talal123";
$password = "Talal123";
$dbname = "ProjetPhpS4";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT id, nom, nouveau_prix FROM produit WHERE quantite > 0");
    $stmt->execute();
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = $_POST['utilisateur_nom'];
        $prenom = $_POST['utilisateur_prenom'];
        $courriel = $_POST['utilisateur_courriel'];
        $produitsSelectionnes = $_POST['produits'];
        $token=  bin2hex(random_bytes(16)); 

        if (!empty($nom) && !empty($prenom) && !empty($courriel) && !empty($produitsSelectionnes)) {
            $total = 0;

            foreach ($produitsSelectionnes as $idProduit => $quantite) {
                $quantite = intval($quantite);
                $stmt = $conn->prepare("SELECT nouveau_prix FROM produit WHERE id = :id");
                $stmt->execute([':id' => $idProduit]);
                $produit = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($produit) {
                    $total += $produit['nouveau_prix'] * $quantite;
                }
            }

            try {
                $stmt = $conn->prepare("INSERT INTO utilisateurs (nom, prenom, courriel,role,token) VALUES (:nom, :prenom, :courriel, 'client', :token)");
                $stmt->execute([':nom' => $nom, ':prenom' => $prenom, ':courriel' => $courriel , ':token' =>$token]);
                $utilisateurId = $conn->lastInsertId();

                $stmt = $conn->prepare("INSERT INTO commandes (utilisateur_id, date_commande, total, statut) 
                                        VALUES (:utilisateur_id, NOW(), :total, 'en cours')");
                $stmt->execute([':utilisateur_id' => $utilisateurId, ':total' => $total]);
                $commandeId = $conn->lastInsertId();

                $stmt = $conn->prepare("INSERT INTO commande_details (commande_id, produit_id, quantite, prix_unitaire) 
                                        VALUES (:commande_id, :produit_id, :quantite, :prix_unitaire)");

                foreach ($produitsSelectionnes as $idProduit => $quantite) {
                    
                    $stmtPrix = $conn->prepare("SELECT nouveau_prix FROM produit WHERE id = :id");
                    $stmtPrix->execute([':id' => $idProduit]);
                    $produit = $stmtPrix->fetch(PDO::FETCH_ASSOC);

                    if ($produit) {
                        $prixUnitaire = $produit['nouveau_prix'];
                    } else {
                        $prixUnitaire = 0;
                    }

                    $stmt->execute([
                        ':commande_id' => $commandeId,
                        ':produit_id' => $idProduit,
                        ':quantite' => $quantite,
                        ':prix_unitaire' => $prixUnitaire,
                    ]);
                }

                $success_message = "Commande enregistrée avec succès !";
            } catch (PDOException $e) {
                $error_message = "Erreur : " . $e->getMessage();
            }
        } else {
            $error_message = "Veuillez remplir tous les champs et sélectionner au moins un produit.";
        }
    }
} catch (PDOException $e) {
    $error_message = "Erreur de connexion à la base de données : " . $e->getMessage();
}
?>





<body class="bg-gray-100">
    <div class="container mx-auto" style="margin-top: 100px;">
        <form method="POST" id="form-commande">
            <div class="grid grid-cols-12 gap-6">
                <!-- Section Messages -->
                <div class="col-span-12">
                    <?php if (isset($success_message)): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                            <?php echo htmlspecialchars($success_message); ?>
                        </div>
                    <?php elseif (isset($error_message)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Colonne gauche -->
                <div class="col-span-4 bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-2xl font-semibold mb-4 text-gray-700">Informations Utilisateur</h2>
                    <div class="mb-4">
                        <label for="utilisateur_nom" class="block text-lg font-medium text-gray-600">Nom :</label>
                        <input type="text" id="utilisateur_nom" name="utilisateur_nom" class="border-gray-300 rounded-md w-full" required>
                    </div>
                    <div class="mb-4">
                        <label for="utilisateur_prenom" class="block text-lg font-medium text-gray-600">Prénom :</label>
                        <input type="text" id="utilisateur_prenom" name="utilisateur_prenom" class="border-gray-300 rounded-md w-full" required>
                    </div>
                    <div class="mb-4">
                        <label for="utilisateur_courriel" class="block text-lg font-medium text-gray-600">courriel :</label>
                        <input type="courriel" id="utilisateur_courriel" name="utilisateur_courriel" class="border-gray-300 rounded-md w-full" required>
                    </div>
                </div>

                <!-- Colonne droite -->
                <div class="col-span-4 bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-2xl font-semibold mb-4 text-gray-700">Recherche Produit</h2>
                    <input type="text" id="search-bar" class="border-gray-300 rounded-md w-full mb-4" placeholder="Rechercher un produit...">
                    <div id="product-list" class="max-h-96 overflow-y-auto">
                        <?php foreach ($produits as $produit): ?>
                            <div class="flex items-center justify-between py-2 border-b border-gray-200">
                                <span class="text-gray-700 font-medium"><?php echo htmlspecialchars($produit['nom']); ?></span>
                                <span class="text-green-600 font-semibold"><?php echo number_format($produit['nouveau_prix'], 2); ?> $</span>
                                <input type="number" name="produits[<?php echo $produit['id']; ?>]"
                                    class="border rounded px-2 py-1 w-16 produit-quantite"
                                    data-id="<?php echo $produit['id']; ?>"
                                    data-prix="<?php echo $produit['nouveau_prix']; ?>"
                                    min="0">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Colonne centrale -->
                <div class="col-span-4 flex flex-col items-center justify-center">
                    <div class="bg-white shadow-md rounded-lg p-6 w-full text-center">
                        <h2 class="text-2xl font-semibold mb-4 text-gray-700">Total Commande</h2>
                        <span id="total" class="text-3xl font-bold text-green-600">0.00 $</span>
                    </div>
                    <button type="submit" class="mt-6 bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 transition">Valider la Commande</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.getElementById("search-bar").addEventListener("input", function(e) {
            const query = e.target.value.toLowerCase();
            const products = document.querySelectorAll("#product-list > div");

            products.forEach(product => {
                const name = product.querySelector("span").textContent.toLowerCase();
                if (name.includes(query)) {
                    product.style.display = "flex";
                } else {
                    product.style.display = "none";
                }
            });
        });

        // update le total 
        const quantiteInputs = document.querySelectorAll(".produit-quantite");
        quantiteInputs.forEach(input => {
            input.addEventListener("input", updateTotal);
        });

        function updateTotal() {
            let total = 0;
            quantiteInputs.forEach(input => {
                const quantite = parseInt(input.value) || 0;
                const prix = parseFloat(input.dataset.prix);
                total += quantite * prix;
            });

            document.getElementById("total").textContent = total.toFixed(2) + " $";
        }
    </script>
</body>