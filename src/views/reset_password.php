<?php
session_start();
$error = "";
$success = "";

$servername = "localhost";
$username = "Talal123";
$password = "Talal123";

try {
    $conn = new PDO("mysql:host=$servername;dbname=ProjetPhpS4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['token'])) {
        $token = $_GET['token'];

        $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE reset_token = ? AND reset_token_expiry > NOW()");
        $stmt->execute([$token]);
        $utilisateur = $stmt->fetch();

        if ($utilisateur) {
            if (isset($_POST['reset_password'])) {
                $nouveau_mot_de_passe = $_POST['new_password'];
                $confirmation_mot_de_passe = $_POST['confirm_password'];

                if (!empty($nouveau_mot_de_passe) && $nouveau_mot_de_passe === $confirmation_mot_de_passe) {

                    $password_hash = password_hash($nouveau_mot_de_passe, PASSWORD_BCRYPT);

                    $update = $conn->prepare("UPDATE utilisateurs SET mot_de_passe = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
                    $update->execute([$password_hash, $utilisateur['id']]);

                    $success = "Votre mot de passe a été mis à jour avec succès.";

                    echo "<script>
                    setTimeout(function() {
                        window.location.href = '/login';
                    }, 2000); 
                </script>";

                } else {
                    $error = "Les mots de passe ne correspondent pas ou sont vides.";
                }
            }
        } else {
            $error = "Lien de réinitialisation invalide ou expiré.";
        }
    } else {
        $error = "Token non fourni.";
    }
} catch (PDOException $e) {
    $error = "Erreur de connexion : " . $e->getMessage();
}
?>





<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h3 class="text-2xl font-semibold text-center text-gray-700 mb-6">Réinitialiser votre mot de passe</h3>
        <p class="text-red-500 text-sm text-center mb-2"><?= $error ?? '' ?></p>
        <p class="text-green-500 text-sm text-center mb-2"><?= $success ?? '' ?></p>

        <form method="post" class="space-y-4">
            <?php if (empty($success)) { ?>
                <input type="password" name="new_password" placeholder="Nouveau mot de passe" required
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <input type="password" name="confirm_password" placeholder="Confirmez le mot de passe" required
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button type="submit" name="reset_password"
                    class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Réinitialiser
                </button>
            <?php } ?>
        </form>
    </div>
</body>


