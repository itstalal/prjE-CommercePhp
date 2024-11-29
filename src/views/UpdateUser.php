<?php
if (isset($_SESSION['utilisateur']['courriel']) && $_SESSION['utilisateur']['role'] !== 'admin') {
    header('Location: /');
    exit();
}

$servername = "localhost";
$username = "Talal123";
$password = "Talal123";
$dbname = "ProjetPhpS4";
$error = "";
$success = "";

// Connexion a la base de donnees
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recuperer les informations de l'utilisateur
    if (isset($params['id'])) {
        $id = $params['id'];
        $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $courriel = $_POST['courriel'];
            $numero_telephone = $_POST['numero_telephone'];
            $role = $_POST['role'];

            // Mise a jour de l'utilisateur
            $stmt = $conn->prepare("UPDATE utilisateurs SET nom = :nom, prenom = :prenom, courriel = :courriel, numero_telephone = :numero_telephone, role = :role WHERE id = :id");
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':courriel', $courriel);
            $stmt->bindParam(':numero_telephone', $numero_telephone);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $success = "Utilisateur mis à jour avec succès !";
            echo "<script>
            setTimeout(function() {
                window.location.href = '/admin-UserManagment';
            }, 1000); 
        </script>";
        }
    }
} catch (PDOException $e) {
    $error = "Erreur : " . $e->getMessage();
}
?>

<!-- Formulaire de mise a jour de l'utilisateur -->
<div class="container mx-auto my-10" style="margin-top: 100px;">
    <div class="max-w-lg mx-auto bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Mettre à jour l'utilisateur</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger text-center mb-4"><?= htmlspecialchars($error); ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success text-center mb-4"><?= htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if (!empty($utilisateur)) : ?>
            <form method="POST" class="space-y-4">
                <div class="mb-3">
                    <label class="block text-gray-700 ">Nom:</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($utilisateur['nom']); ?>"
                        class="form-control px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>

                <div class="mb-3">
                    <label class="block text-gray-700 ">Prénom:</label>
                    <input type="text" name="prenom" value="<?= htmlspecialchars($utilisateur['prenom']); ?>"
                        class="form-control px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>

                <div class="mb-3">
                    <label class="block text-gray-700 ">Email:</label>
                    <input type="email" name="courriel" value="<?= htmlspecialchars($utilisateur['courriel']); ?>"
                        class="form-control px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>

                <div class="mb-3">
                    <label class="block text-gray-700 ">Numéro:</label>
                    <input type="text" name="numero_telephone" value="<?= htmlspecialchars($utilisateur['numero_telephone']); ?>"
                        class="form-control px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>

                <div class="mb-3">
                    <label class="block text-gray-700 ">Rôle:</label>
                    <input type="text" name="role" value="<?= htmlspecialchars($utilisateur['role']); ?>"
                        class="form-control px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                        required <?php if ($utilisateur['role'] == 'admin') {
                                        echo 'readonly';
                                    }  ?>>
                </div>

                <div class="text-center mt-6">
                    <a href="<?= $router->generate('manag_user'); ?>" class="btn btn-danger w-full py-2 font-semibold">Annulé</a>
                    <button type="submit" class="btn btn-primary w-full py-2 font-semibold mt-6">Mettre à jour</button>

                </div>
            </form>
        <?php else : ?>
            <p class="text-center text-red-600">Utilisateur non trouvé.</p>
        <?php endif; ?>
    </div>
</div>