<?php
require '../vendor/autoload.php';

$uri = $_SERVER['REQUEST_URI'];

$router = new AltoRouter();


// route systems
//clients
$router->map('GET', '/', 'home', 'accueil');
$router->map('GET', '/contact', 'contact', 'contacter');
$router->map('GET', '/login', 'login', 'login');
$router->map('POST', '/login', 'login', 'log');
$router->map('GET', '/register', 'register', 'register');
$router->map('POST', '/register', 'register', 'regist');
$router->map('GET', '/logout', 'logout', 'logout');
$router->map('GET', '/produits', 'produit', 'produit');
$router->map('GET', '/produits-[i:id]', 'produitsDetails', 'produ');
$router->map('GET', '/cart', 'cart', 'cart');
//  
//admin
$router->map('GET', '/admin', 'admin', 'admin');
$router->map('GET', '/admin-UserManagment', 'manag_user', 'manag_user');
$router->map('GET', '/admin-ProductManagment', 'manag_product', 'manag_product');
// le name article === update-user c'est juste le nom article est enregistre comme chemin de updateUser alors on peut pas le changer
$router->map('GET|POST', '/update-user:[i:id]', 'UpdateUser', 'article'); 
$router->map('GET|POST', '/delete-user-[i:id]', 'DeleteUser', 'DeleteUser');
$router->map('GET|POST', '/add-Product', 'addProduct','addProduct');
$router->map('GET|POST','/delete-product-[i:id]','DeleteProduct','DeleteProduct');
$router->map('GET|POST','/update-product-[i:id]','UpdateProduct','UpdateProduct');
//fin admin
$router->map('GET|POST','/ajouter-panier','AjouterPanier','AjouterPanier');
$router->map('GET|POST','/Delete-item-[i:id]','DeleteItemCart','DeleteItemCart');
$router->map('GET|POST','/Confirm-Order','ConfirmOrder','ConfirmOrder');
$router->map('GET|POST','/checkout','checkout','checkout');

// route match
$match = $router->match();
// dump($match);
if (is_array($match)) {
    require '../static/header.php';
    if (is_callable($match['target'])) {
        call_user_func_array($match['target'], $match['params']);
    } else {
        $params = $match['params'];
        require "../src/views/{$match['target']}.php";
    }
    require '../static/footer.php';
} else {
    require '../src/views/errors/404.php';
}
?>
<!-- // $match["target"]($match['params']['slug'],$match['params']['id']); -->
<!-- // dump($_SERVER);
// if($uri === '/nous-contacter'){
//     require '../views/contact.php';
// }elseif($uri === '/'){
//     require '../views/home.php';
// }else{
//     echo 404;
// } -->