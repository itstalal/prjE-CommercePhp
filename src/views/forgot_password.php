<?php
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
                $token = bin2hex(random_bytes(16));
                $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

                $update = $conn->prepare("UPDATE utilisateurs SET reset_token = ?, reset_token_expiry = ? WHERE courriel = ?");
                $update->execute([$token, $expiry, $email]);

                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password?token=" . $token;

                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'gortex969@gmail.com'; 
                    $mail->Password = 'hpsf kqib awno lulb'; 
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('gortex969@gmail.com', 'Projet PHP'); 
                    $mail->addAddress($email); 

                    $mail->isHTML(true);
                    $mail->Subject = "Réinitialisation de mot de passe";
                    $mail->Body = "Cliquez sur ce lien pour réinitialiser votre mot de passe : <a href='$reset_link'>$reset_link</a>";

                    $mail->send();
                    $success = "Un email avec un lien de réinitialisation a été envoyé.";
                } catch (Exception $e) {
                    $error = "Erreur lors de l'envoi de l'email : " . $mail->ErrorInfo;
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


<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h3 class="text-2xl font-semibold text-center text-gray-700 mb-6">Demander une réinitialisation de mot de passe</h3>
        <p class="text-red-500 text-sm text-center mb-2"><?= $error ?? '' ?></p>
        <p class="text-green-500 text-sm text-center mb-2"><?= $success ?? '' ?></p>
        <form method="post" class="space-y-4">
            <input type="email" name="email" placeholder="Entrez votre e-mail" required
                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button type="submit" name="reset_request"
                class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Envoyer
            </button>
        </form>
    </div>
</body>




