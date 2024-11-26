<?php

use League\OAuth2\Client\Provider\Google;

session_start();

$googleProvider = new Google([
    'clientId'     => '439916367175-ilc2uimherdufaltbvtggq2kdv102n7c.apps.googleusercontent.com',
    'clientSecret' => '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',
    'redirectUri'  => 'http://localhost/google-callback',
]);

if (!isset($_GET['code'])) {
    $authUrl = $googleProvider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $googleProvider->getState();
    header('Location: ' . $authUrl);
    exit;
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state');
} else {
    try {
        // get un access token
        $token = $googleProvider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);
    
        // get les informations utilisateur
        $googleUser = $googleProvider->getResourceOwner($token);
        $userData = $googleUser->toArray(); // Obtenir les données utilisateur
    
        // Extraire les informations 
        $email = $userData['email'];
        $prenom = $userData['given_name'];
        $nom = $userData['family_name'];
    
        $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE courriel = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$user) {
            $stmt = $conn->prepare("INSERT INTO utilisateurs (prenom, nom, courriel, role, token) VALUES (?, ?, ?, ?, ?)");
            $token = bin2hex(random_bytes(16)); // Générer un token pour cet utilisateur
            $stmt->execute([$prenom, $nom, $email, 'client', $token]);
        }
    
        $_SESSION['user_email'] = $email;
        header('Location: /');
        exit;
    } catch (Exception $e) {
        exit('Échec de la récupération des informations utilisateur : ' . $e->getMessage());
    }
    
}
