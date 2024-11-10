<?php

if (isset($_SESSION['utilisateur']['courriel']) && $_SESSION['utilisateur']['role'] !== 'admin') {
    header('Location: /');
    exit();
} else {
?> 
    <section>
        

        <!-- section des cartes produits -->
        <div class="product-grid">
            <div class="product-card">

                <img src="assets/images/produit.webp" alt="Montre Rolex" class="product-image " />
                <p class="product-name">Gérer les Produits</p>
                <div>
                    <a href="<?= $router->generate('manag_product'); ?>" class="btn btn-primary fs-3"><i class="bi bi-eye"></i></a>
                    <a href="<?= $router->generate('addProduct'); ?>" class="btn btn-success fs-3"><i class="bi bi-plus-lg"></i></a>
                </div>

            </div>
        </div>

        <div class="product-grid">
            <div class="product-card">

                <img src="assets/images/user.webp" alt="Montre Rolex" class="product-image " />
                <p class="product-name">Gérer les Utilisateurs</p>
                <div>
                    <a href="<?= $router->generate('manag_user'); ?>" class="btn btn-primary fs-3"><i class="bi bi-eye"></i></a>
                </div>

            </div>
        </div>

        <div class="product-grid">
            <div class="product-card">

                <img src="assets/images/order_managment.jpg" alt="Montre Rolex" class="product-image " />
                <p class="product-name">Gérer les Commandes</p>
                <div>
                    <a href="" class="btn btn-primary fs-3"><i class="bi bi-eye"></i></a>
                </div>

            </div>
        </div>

       
    </section>
<?php
}
?>