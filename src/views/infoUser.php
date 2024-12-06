<?php
session_start();

if (!isset($_SESSION['utilisateur']['id'])) {
    header('Location: /login');
    exit();
}

$servername = "localhost";
$username = "Talal123";
$password = "Talal123";
$dbname = "ProjetPhpS4";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $utilisateur_id = $_SESSION['utilisateur']['id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nouveau_nom = $_POST['nom'];
        $nouveau_prenom = $_POST['prenom'];
        $nouveau_courriel = $_POST['courriel'];
        $nouveau_telephone = $_POST['numero_telephone'];

        $stmt = $conn->prepare("UPDATE utilisateurs SET nom = :nom, prenom = :prenom, courriel = :courriel, numero_telephone = :numero_telephone WHERE id = :id");
        $stmt->bindParam(':nom', $nouveau_nom);
        $stmt->bindParam(':prenom', $nouveau_prenom);
        $stmt->bindParam(':courriel', $nouveau_courriel);
        $stmt->bindParam(':numero_telephone', $nouveau_telephone);
        $stmt->bindParam(':id', $utilisateur_id);
        $stmt->execute();

        header('Location: /infoUser');
        exit();
    }

    // Récupération des informations utilisateur
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE id = :utilisateur_id");
    $stmt->bindParam(':utilisateur_id', $utilisateur_id);
    $stmt->execute();
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT * FROM commandes WHERE utilisateur_id = :utilisateur_id ORDER BY date_commande DESC");
    $stmt->bindParam(':utilisateur_id', $utilisateur_id);
    $stmt->execute();
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    ?>
    
    <body class="bg-gray-100">
        <div class="container mt-5">
            <!-- Profil Utilisateur -->
            <div class="bg-white shadow-md rounded p-4 mb-5">
                <h2 class="text-xl text-center font-bold text-gray-700 mb-4">Profil Utilisateur</h2>
                <form method="POST" class="row g-3">
                    <div class="col-md-6">
                        <label for="nom" class="block text-lg font-medium text-gray-600 mb-1">Nom</label>
                        <input type="text" id="nom" name="nom" class="form-control" value="<?= htmlspecialchars($utilisateur['nom']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="nom" class="block text-lg font-medium text-gray-600 mb-1">Prenom</label>
                        <input type="text" id="nom" name="prenom" class="form-control" value="<?= htmlspecialchars($utilisateur['prenom']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="courriel" class="block text-lg font-medium text-gray-600 mb-1">Courriel</label>
                        <input type="email" id="courriel" name="courriel" class="form-control" value="<?= htmlspecialchars($utilisateur['courriel']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="numero_telephone" class="block text-lg font-medium text-gray-600 mb-1">Numéro de Téléphone</label>
                        <input type="text" id="numero_telephone" name="numero_telephone" class="form-control" value="<?= htmlspecialchars($utilisateur['numero_telephone']) ?>" required>
                    </div>
                    <div class="col-12 text-center mt-3">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>

            <!-- Historique des Commandes -->
            <div class="bg-white shadow-md rounded p-4">
                <h2 class="text-xl text-center font-bold text-gray-700 mb-4">Historique des Commandes</h2>
                <?php if (!empty($commandes)) : ?>
                    <table class="table table-striped">
                        <thead class="bg-blue-500 text-white">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($commandes as $index => $commande): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($commande['date_commande']) ?></td>
                                    <td><?= number_format($commande['total'], 2) ?> $</td>
                                    <td><?= htmlspecialchars($commande['statut']) ?></td>
                                    
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <div class="alert alert-warning text-center">
                        Vous n'avez aucune commande pour le moment.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Boutons -->
            <div class="text-center mt-4">
                <a href="/" class="btn btn-success">Retour à l'accueil</a>
                <a href="/logout" class="btn btn-danger">Déconnexion</a>
            </div>
        </div>
    </body>
    </html>
    <?php
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
