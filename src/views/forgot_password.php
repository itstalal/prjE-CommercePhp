<?php
$error = "";
$success = "";
$servername = "localhost";
$username = "Talal123";
$password = "Talal123";

try {
    $conn = new PDO("mysql:host=$servername;dbname=ProjetPhpS4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['reset_request'])) {
        $email = $_POST['email'];
        if (!empty($email)) {
            $requete = $conn->prepare("SELECT * FROM utilisateurs WHERE courriel = ?");
            $requete->execute([$email]);
            $utilisateur = $requete->fetch();

            if ($utilisateur) {
                $token = bin2hex(random_bytes(16));
                $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

                $update = $conn->prepare("UPDATE utilisateurs SET reset_token = ?, reset_token_expiry = ? WHERE courriel = ?");
                $update->execute([$token, $expiry, $email]);

               
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . $router->generate('reset_password', ['token' => $token]);


                mail($email, "Réinitialisation de mot de passe", "Cliquez sur ce lien pour réinitialiser votre mot de passe : $reset_link");
                

                $success = "Un email avec un lien de réinitialisation a été envoyé.";
            } else {
                $error = "Cet email n'existe pas dans notre base de données.";
            }
        } else {
            $error = "Veuillez entrer une adresse e-mail.";
        }
    }
} catch (PDOException $e) {
    $error = "Erreur de connexion : " . $e->getMessage();
}
?>
<form method="post" style="margin-top: 200px;">
    <h3>Demander une réinitialisation de mot de passe</h3>
    <p class="text-danger"><?= $error ?></p>
    <p class="text-success"><?= $success ?></p>
    <input type="email" name="email" placeholder="Entrez votre e-mail" required>
    <button type="submit" name="reset_request">Envoyer</button>
</form>
