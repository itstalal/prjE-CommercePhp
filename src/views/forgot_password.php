<section>
<?php
require 'vendor/autoload.php'; // Inclure PHPMailer via Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
                // Générer un token unique et définir sa date d'expiration
                $token = bin2hex(random_bytes(16));
                $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

                // Mettre à jour le token dans la base de données
                $update = $conn->prepare("UPDATE utilisateurs SET reset_token = ?, reset_token_expiry = ? WHERE courriel = ?");
                $update->execute([$token, $expiry, $email]);

                // Générer le lien de réinitialisation
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=$token";

                // Configuration de PHPMailer
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com'; // Serveur SMTP de Gmail
                    $mail->SMTPAuth = true;
                    $mail->Username = 'elmoujahidtalal5@gmail.com'; // Votre adresse Gmail
                    $mail->Password = 'kyob kedr zoqr xlld'; // Votre mot de passe d'application Gmail
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Expéditeur et destinataire
                    $mail->setFrom('elmoujahidtalal5@gmail.com', 'Votre Nom');
                    $mail->addAddress($email);

                    // Contenu de l'email
                    $mail->isHTML(true);
                    $mail->Subject = 'Réinitialisation de mot de passe';
                    $mail->Body = "Cliquez sur ce lien pour réinitialiser votre mot de passe : <a href='$reset_link'>$reset_link</a>";

                    // Envoi de l'email
                    $mail->send();
                    $success = "Un email avec un lien de réinitialisation a été envoyé.";
                } catch (Exception $e) {
                    $error = "L'email n'a pas pu être envoyé. Erreur : " . $mail->ErrorInfo;
                }
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

</section>