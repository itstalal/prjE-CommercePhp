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

    // Récupération des commandes de l'utilisateur
    $stmt = $conn->prepare("SELECT * FROM commandes WHERE utilisateur_id = :utilisateur_id ORDER BY date_commande DESC");
    $stmt->bindParam(':utilisateur_id', $utilisateur_id);
    $stmt->execute();
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Affichage des commandes
    ?>
   
    <body class="bg-gray-100">
        <div class="container mt-5">
            <h1 class="text-center text-2xl font-bold mb-4">Mes Commandes</h1>
            <?php if (!empty($commandes)) : ?>
                <table class="table table-striped">
                    <thead class="bg-blue-500 text-white">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commandes as $index => $commande): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($commande['date_commande']) ?></td>
                                <td><?= number_format($commande['total'], 2) ?> $</td>
                                <td><?= htmlspecialchars($commande['statut']) ?></td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="alert('Détails de la commande #<?= $commande['id'] ?> non implémentés.')">Voir Détails</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="text-center mt-4">
                    <a href="/" class="btn btn-success">Retour à l'accueil</a>
                </div>
            <?php else : ?>
                <div class="alert alert-warning text-center">
                    Vous n'avez aucune commande pour le moment.
                </div>
                <div class="text-center mt-4">
                    <a href="/produits" class="btn btn-primary">Explorer les produits</a>
                </div>
            <?php endif; ?>
        </div>
    </body>
    </html>
    <?php
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
