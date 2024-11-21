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
    // Connexion à la base de données
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Mise à jour du statut si le formulaire a été soumis
    if (isset($_POST['update_status'])) {
        $commande_id = $_POST['commande_id'];
        $new_status = $_POST['statut'];

        // Mise à jour du statut dans la base de données
        $stmt = $conn->prepare("UPDATE commandes SET statut = :statut WHERE id = :commande_id");
        $stmt->bindParam(':statut', $new_status);
        $stmt->bindParam(':commande_id', $commande_id);
        $stmt->execute();
    }

    // Récupérer toutes les commandes avec les utilisateurs
    $stmt = $conn->prepare("SELECT commandes.id AS commande_id, utilisateurs.nom AS utilisateur_nom, utilisateurs.prenom AS utilisateur_prenom, 
                            commandes.date_commande, commandes.total, commandes.statut
                            FROM commandes 
                            JOIN utilisateurs ON commandes.utilisateur_id = utilisateurs.id");
    $stmt->execute();
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_message = "Erreur de connexion à la base de données : " . $e->getMessage();
}
?>

<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between mb-4">
        <h2 class="text-center text-3xl font-semibold">Toutes les Commandes</h2>
        <a href="<?= $router->generate('add_order'); ?>" class="btn btn-warning">Ajouter une Nouvelle Commande</a>
    </div>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger mt-4">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($commandes)): ?>
        <div class="overflow-x-auto mt-4">
            <table class="table table-striped table-bordered shadow-lg">
                <thead class="bg-dark text-white">
                    <tr>
                        <th scope="col">ID Commande</th>
                        <th scope="col">Nom Utilisateur</th>
                        <th scope="col">Prénom Utilisateur</th>
                        <th scope="col">Date de Commande</th>
                        <th scope="col">Total</th>
                        <th scope="col">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $commande): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($commande['commande_id']); ?></td>
                            <td><?php echo htmlspecialchars($commande['utilisateur_nom']); ?></td>
                            <td><?php echo htmlspecialchars($commande['utilisateur_prenom']); ?></td>
                            <td><?php echo htmlspecialchars($commande['date_commande']); ?></td>
                            <td><?php echo htmlspecialchars($commande['total']); ?> $</td>
                            <td>
                                <span><?php echo htmlspecialchars($commande['statut']); ?></span>
                                <input type="checkbox" class="form-check-input" id="toggle_<?php echo $commande['commande_id']; ?>" onclick="toggleForm(<?php echo $commande['commande_id']; ?>)">
                                <label for="toggle_<?php echo $commande['commande_id']; ?>"></label>
                                
                                <div id="form_<?php echo $commande['commande_id']; ?>" style="display: none;">
                                    <form method="POST">
                                        <input type="hidden" name="commande_id" value="<?php echo $commande['commande_id']; ?>">
                                        <select name="statut" class="form-select">
                                            <option value="en cours" <?php echo ($commande['statut'] == 'en cours') ? 'selected' : ''; ?>>En cours</option>
                                            <option value="expédiée" <?php echo ($commande['statut'] == 'expédiée') ? 'selected' : ''; ?>>Expédiée</option>
                                            <option value="livrée" <?php echo ($commande['statut'] == 'livrée') ? 'selected' : ''; ?>>Livrée</option>
                                            <option value="annulée" <?php echo ($commande['statut'] == 'annulée') ? 'selected' : ''; ?>>Annulée</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-sm btn-primary mt-2">Mettre à jour</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-center text-xl mt-4">Aucune commande trouvée.</p>
    <?php endif; ?>
</div>

<script>
    function toggleForm(commande_id) {
        var form = document.getElementById('form_' + commande_id);
        var checkbox = document.getElementById('toggle_' + commande_id);
        if (checkbox.checked) {
            form.style.display = 'block'; 
        } else {
            form.style.display = 'none'; 
        }
    }
</script>
