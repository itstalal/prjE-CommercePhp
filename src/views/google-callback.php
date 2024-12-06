<?php

require 'vendor/autoload.php'; // Charge automatiquement les dépendances si tu utilises Composer.
use League\OAuth2\Client\Provider\Google;

session_start();

$googleProvider = new Google([
    'clientId'     => '439916367175-ilc2uimherdufaltbvtggq2kdv102n7c.apps.googleusercontent.com',
    'clientSecret' => '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',
    'redirectUri'  => 'http://localhost:8000/google-callback',
]);

try {
    // Vérifier le paramètre "state" pour éviter les attaques CSRF
    if (!isset($_GET['state']) || $_GET['state'] !== $_SESSION['oauth2state']) {
        unset($_SESSION['oauth2state']);
        exit('Invalid state');
    }

    // Obtenir le token d'accès
    $token = $googleProvider->getAccessToken('authorization_code', [
        'code' => $_GET['code'],
    ]);

    // Récupérer les informations utilisateur
    $googleUser = $googleProvider->getResourceOwner($token);
    $userData = $googleUser->toArray();

    // Connexion à la base de données
    $conn = new PDO("mysql:host=localhost;dbname=ProjetPhpS4", "Talal123", "Talal123");

    $email = $userData['email'];
    $prenom = $userData['given_name'];
    $nom = $userData['family_name'];

    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE courriel = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Si l'utilisateur n'existe pas encore, l'ajouter
        $token = bin2hex(random_bytes(16));
        $stmt = $conn->prepare("INSERT INTO utilisateurs (prenom, nom, courriel, role, token) VALUES (?, ?, ?, 'client', ?)");
        $stmt->execute([$prenom, $nom, $email, $token]);
    }

    $_SESSION['utilisateur'] = $user ?: ['prenom' => $prenom, 'nom' => $nom, 'courriel' => $email];
    header('Location: /dashboard'); // Rediriger après l'authentification réussie
    exit;

} catch (Exception $e) {
    echo 'Erreur : ' . $e->getMessage();
}
?>
