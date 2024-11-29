<?php
$servername = "localhost";
$username = "Talal123";
$password = "Talal123";
$error = "";


try {
    $conn = new PDO("mysql:host=$servername;dbname=ProjetPhpS4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_SESSION['utilisateur'])) {
        header('Location: /');
    }

    if (isset($_POST['connexion'])) {
        $email = $_POST['email'];
        $password = $_POST['mot_de_passe'];

        if (!empty($email) && !empty($password)) {
            try {
                $requete = $conn->prepare("SELECT * FROM utilisateurs WHERE courriel = ?");
                $requete->execute([$email]);
                $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

                if ($utilisateur) {
                    if (password_verify($password, $utilisateur['mot_de_passe'])) {
                        unset($utilisateur['mot_de_passe']);
                        $_SESSION['utilisateur'] = $utilisateur;
                        $_SESSION['id'] = $utilisateur['id'];
                        $_SESSION['email'] = $utilisateur['courriel'];
                        header('Location: /');
                    } else {
                        $error = "Mot de passe incorrect.";
                    }
                } else {
                    $error = "Aucun utilisateur trouvé avec ce courriel.";
                }
            } catch (PDOException $e) {
                $error = "Erreur lors de la connexion : " . $e->getMessage();
            }
        } else {
            $error = "Veuillez remplir tous les champs.";
        }
    }
} catch (PDOException $e) {
    $error = "Échec de la connexion : " . $e->getMessage();
}


?>




<section style="overflow: hidden;">
    <div class="card text-center">
        <div class="card-header b-2">
            <a href="<?= $router->generate('register'); ?>" class="text-decoration-none text-primary">Pas encore créé de compte ?</a>
        </div>

        <form method="POST">
            <div class="card-body" style="width: 500px; height: 300px;">
                <h3 class="card-title text-start fs-3">Se connecter</h3>
                <h4 class="text-white text-small text-center bg-danger"><?php if (isset($error)) {
                                                                            echo $error;
                                                                        } ?></h4>

                <div class="mb-3">
                    <label class="fs-6 text-start d-block">Adresse e-mail</label>
                    <input type="email" class="form-control w-50" id="exampleFormControlInput1" placeholder="nom@exemple.com" name="email">
                </div>
                <div class="mb-4">
                    <label class="fs-6 text-start d-block">Mot de passe</label>
                    <input type="password" class="form-control w-50" placeholder="Entrer votre mot de passe" name="mot_de_passe">
                </div>
                <div class="text-start d-block">
                    <input type="submit" class="btn btn-primary" value="Se connecter" name="connexion">
                </div>

            </div>
            <!-- <div class="text-start d-block">
                <a href="/google-callback" class="btn btn-danger">Connectez-vous avec Google</a>
            </div> -->
            <div class="card-footer text-body-secondary">
                <a href="<?= $router->generate('forgot_password'); ?>" class="text-decoration-none text-primary">Mot de passe oublié?</a>
            </div>

        </form>
    </div>
</section>