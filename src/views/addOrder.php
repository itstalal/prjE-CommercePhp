<?php
if (isset($_SESSION['utilisateur']['courriel']) && $_SESSION['utilisateur']['role'] !== 'admin') {
    header('Location: /');
    exit();
}

$servername = "localhost";
$username = "Talal123";
$password = "Talal123";
$dbname = "ProjetPhpS4";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer tous les produits disponibles
    $stmt = $conn->prepare("SELECT id, nom, nouveau_prix FROM produit");
    $stmt->execute();
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Erreur de connexion à la base de données : " . $e->getMessage();
}
?>


<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto " style="margin-top: 100px;">
        <div class="grid grid-cols-12 gap-6">
            <!-- Colonne gauche : Formulaire utilisateur -->
            <div class="col-span-4 bg-white shadow-md rounded-lg p-6">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Ajouter un Utilisateur</h2>
                <form id="form-utilisateur">
                    <div class="mb-4">
                        <label for="utilisateur_nom" class="block text-lg font-medium text-gray-600 mb-1">Nom :</label>
                        <input type="text" id="utilisateur_nom" name="utilisateur_nom" class="border-gray-300 rounded-md w-full" required>
                    </div>
                    <div class="mb-4">
                        <label for="utilisateur_prenom" class="block text-lg font-medium text-gray-600 mb-1">Prénom :</label>
                        <input type="text" id="utilisateur_prenom" name="utilisateur_prenom" class="border-gray-300 rounded-md w-full" required>
                    </div>
                    <div class="mb-4">
                        <label for="utilisateur_email" class="block text-lg font-medium text-gray-600 mb-1">Email :</label>
                        <input type="email" id="utilisateur_email" name="utilisateur_email" class="border-gray-300 rounded-md w-full" required>
                    </div>
                </form>
            </div>

            <!-- Colonne droite : Recherche produit -->
            <div class="col-span-4 bg-white shadow-md rounded-lg p-6">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Recherche Produit</h2>
                <input type="text" id="search-bar" class="border-gray-300 rounded-md w-full mb-4" placeholder="Rechercher un produit...">
                <div id="product-list" class="max-h-96 overflow-y-auto">
                    <?php foreach ($produits as $produit): ?>
                        <div class="flex items-center justify-between py-2 border-b border-gray-200">
                            <span class="text-gray-700 font-medium"><?php echo htmlspecialchars($produit['nom']); ?></span>
                            <span class="text-green-600 font-semibold"><?php echo htmlspecialchars($produit['nouveau_prix']); ?> $</span>
                            <button class="add-product-btn bg-blue-500 text-white px-2 py-1 rounded" data-id="<?php echo $produit['id']; ?>" data-name="<?php echo htmlspecialchars($produit['nom']); ?>" data-price="<?php echo $produit['nouveau_prix']; ?>">Ajouter</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Colonne centrale : Total et validation -->
            <div class="col-span-4 flex flex-col items-center justify-center">
                <div class="bg-white shadow-md rounded-lg p-6 w-full text-center">
                    <h2 class="text-2xl font-semibold mb-4 text-gray-700">Total Commande</h2>
                    <span id="total" class="text-3xl font-bold text-green-600">0.00 $</span>
                </div>
                <button id="validate-order" class="mt-6 bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 transition">Valider la Commande</button>
            </div>
        </div>
    </div>

    <script>
        const produits = <?php echo json_encode($produits); ?>;
        const productList = document.getElementById('product-list');
        const searchBar = document.getElementById('search-bar');
        const totalElement = document.getElementById('total');
        let total = 0;
        const selectedProducts = [];

        // Ajouter des produits sélectionnés
        document.querySelectorAll('.add-product-btn').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const price = parseFloat(button.getAttribute('data-price'));

                selectedProducts.push({
                    id,
                    name,
                    price
                });
                total += price;
                totalElement.textContent = total.toFixed(2) + ' $';
            });
        });

        // Recherche dynamique
        searchBar.addEventListener('input', () => {
            const query = searchBar.value.toLowerCase();
            productList.innerHTML = '';
            produits.forEach(produit => {
                if (produit.nom.toLowerCase().includes(query)) {
                    const productHTML = `
                        <div class="flex items-center justify-between py-2 border-b border-gray-200">
                            <span class="text-gray-700 font-medium">${produit.nom}</span>
                            <span class="text-green-600 font-semibold">${produit.nouveau_prix} $</span>
                            <button class="add-product-btn bg-blue-500 text-white px-2 py-1 rounded" data-id="${produit.id}" data-name="${produit.nom}" data-price="${produit.nouveau_prix}">Ajouter</button>
                        </div>
                    `;
                    productList.innerHTML += productHTML;
                }
            });
        });

        // Envoi des données au serveur

        document.getElementById('validate-order').addEventListener('click', () => {
            const utilisateur = {
                nom: document.getElementById('utilisateur_nom').value,
                prenom: document.getElementById('utilisateur_prenom').value,
                email: document.getElementById('utilisateur_email').value
            };

            fetch('/admin-save-order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        utilisateur,
                        produits: selectedProducts,
                        total
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Commande enregistrée avec succès !');
                    } else {
                        alert('Erreur : ' + data.message);
                    }
                });
        });
    </script>


</body>

</html>