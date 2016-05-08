<?php
session_start();
setcookie('pseudo','',time()-3600);
setcookie('password','',time()-3600);
//VIDER LES VARIABLES DE SESSION
$_SESSION = array();
//DESTRUCTION DE LA SESSION
session_destroy();
//REDIRECTION VERS LA PAGE home.php QUI CORRESPOND A LA PAGE DE CONNEXION/INSCRIPTION
header("Location: home.php");
?>