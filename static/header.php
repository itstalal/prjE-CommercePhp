<?php session_start();


$servername = "localhost";
$username = "Talal123";
$password = "Talal123";
$dbname = "ProjetPhpS4";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $utilisateur_id = $_SESSION['utilisateur']['id'];
    $stmt = $conn->prepare("SELECT SUM(quantite) AS total_quantite FROM panier WHERE utilisateur_id = :utilisateur_id");
    $stmt->bindParam(':utilisateur_id', $utilisateur_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $total_quantite = $result['total_quantite'] ? $result['total_quantite'] : 0;
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gortex</title>
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <svg class="bi" width="32" height="32" fill="currentColor">
        <use xlink:href="bootstrap-icons.svg#shop" />
    </svg>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="assets/css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/product.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>


</head>

<body>
    <header>
        <input type="checkbox" name="" id="chek">
        <label for="chek"><i class="fa-solid fa-bars"></i></label>
        <h1><a href="<?= $router->generate('accueil'); ?>" class="logo">GORTEX</a></h1>
        <nav>
            <a href="<?= $router->generate('accueil'); ?>">Accueil</a>
            <a href="<?= $router->generate('produit') ?>">Produits</a>
            <a href="<?= $router->generate('produit') ?>">Categorie</a>
            <a href="<?= $router->generate('contacter') ?>">Contact</a>
        </nav>
        <div class="menu-right">
            <a href="<?= $router->generate('cart'); ?>" class="pb-2 btn fs-4" style="border:none;"><i class="bi bi-bag-fill"></i> <?php
                                                                                                                                    if (isset($_SESSION['utilisateur']['id'])) {
                                                                                                                                        echo $total_quantite;
                                                                                                                                    }
                                                                                                                                    ?></a>

            <?php if ($_SESSION['utilisateur']) { ?>
                <div class="dropdown">
                    <button class="btn fs-2" style="border:none;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-fill-gear"></i>
                    </button>

                    <ul class="dropdown-menu">
                        <?php if (isset($_SESSION['utilisateur']['courriel']) && $_SESSION['utilisateur']['role'] == 'admin') { ?>
                            <li><a class="dropdown-item" href="<?= $router->generate('admin'); ?>">Admin</a></li>
                        <?php } ?>

                        <li><a class="dropdown-item" href="<?= $router->generate('infoUser'); ?>">Informations</a></li>
                        <li><a href="<?= $router->generate('logout'); ?>" class="dropdown-item text-danger">Se deconnecté <i class="bi bi-box-arrow-right"></i></a></li>

                    </ul>
                </div>


            <?php   } else { ?>
                <a href="<?= $router->generate('login'); ?>" class="btn fs-1" style="border:none;"><i class="bi bi-person-fill"></i></a>
            <?php } ?>
        </div>
    </header>
</body>

</html>