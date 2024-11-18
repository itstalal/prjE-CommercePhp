<?php
if(isset($_SESSION['utilisateur'])){
    header('Location: /');
}
$servername = "localhost";
$username = "Talal123";
$password = "Talal123";
$error = "";

try {
    $conn = new PDO("mysql:host=$servername;dbname=ProjetPhpS4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['add_user'])) {
        $prenom = $_POST['prenom'];
        $nom = $_POST['nom'];
        $tele = $_POST['telephone'];
        $email = $_POST['email'];
        $password = $_POST['mot_de_passe'];
        $c_password = $_POST['c_mot_de_passe'];

        if (!empty($prenom) && !empty($nom) && !empty($tele) && !empty($email) && !empty($password) && !empty($c_password)) {
            
            if ($password === $c_password) {
                try {
                    $password = password_hash($password, PASSWORD_DEFAULT);
                    $requete = $conn->prepare("INSERT INTO utilisateurs (prenom, nom, numero_telephone, courriel, mot_de_passe, role) VALUES (?, ?, ?, ?, ?, ?)");
                    $requete->execute([$prenom, $nom, $tele, $email, $password, 'client']);
                    header('Location: /login');
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) { 
                        $error = "Ce courriel est déjà utilisé. Veuillez en choisir un autre.";
                    } else {
                        $error = "Erreur lors de l'inscription : " . $e->getMessage();
                    }
                }
            } else {
                $error = "Les mots de passe ne correspondent pas.";
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
            <a href="<?= $router->generate('login') ?>" class="text-decoration-none text-primary">Vous avez déjà un compte ?</a> 
        </div>

        <form  method="post">
            <div class="card-body" style="width: 500px; height: auto;">
                <h3 class=" fs-3 text-start b-3"><b>S'inscrire</b></h3>
                <h4 class="text-white text-small text-center bg-danger"><?php if($error){ echo $error; } ?></h4>
                <div class="mb-3">
                    <label class="fs-6 text-start d-block">Prenom<b class="text-danger">*</b></label>
                    <input type="text" class="form-control w-50" placeholder="Prenom" name="prenom" required>
                </div>
                <div class="mb-3">
                    <label class="fs-6 text-start d-block">Nom<b class="text-danger">*</b></label>
                    <input type="text" class="form-control w-50" placeholder="Nom" name="nom" required>
                </div>
                <div class="mb-3">
                    <label class="fs-6 text-start d-block">Numéro de téléphone<b class="text-danger">*</b></label>
                    <input type="tel" class="form-control w-50" placeholder="xxx-xxx-xxxx" name="telephone" required>
                </div>
                <div class="mb-3">
                    <label class="fs-6 text-start d-block">Adresse e-mail<b class="text-danger">*</b></label>
                    <input type="email" class="form-control w-50"  placeholder="nom@exemple.com" name="email" required>
                </div>
                <div class="mb-4">
                    <label class="fs-6 text-start d-block">Mot de passe<b class="text-danger">*</b></label>
                    <input type="password" class="form-control w-50" placeholder="Entrer votre mot de passe" name="mot_de_passe" required>
                </div>
                <div class="mb-4">
                    <label class="fs-6 text-start d-block">Confirmer votre mot de passe<b class="text-danger">*</b></label>
                    <input type="password" class="form-control w-50" placeholder="Confirmer votre mot de passe" name="c_mot_de_passe" required>
                </div>
                <div class="text-start d-block ">
                <input type="submit" class="btn btn-primary" name="add_user" value="S'inscrire">
                </div>
            </div>
        </form>
    </div>
</section>




