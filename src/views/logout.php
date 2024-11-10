<?php 
if(isset($_SESSION['utilisateur'])){
    unset($_SESSION['utilisateur']);
}
session_destroy();
header('Location: /');
?>