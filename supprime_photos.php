<?php
//RECUPERATION DES VARIABLES DE SESSION
session_start();

//CONNEXION BDD
$bdd = new PDO('mysql:host=localhost;dbname=phplogin', 'root', '');

//TEST: VERIFICATION QUE LA VARIABLE ID EXISTE ET SUPERIEUR A 0
if (isset($_GET['id'])AND $_GET['id'] > 0)
{
	//REQUETE: SUPPRESSION DE LA PHOTO SELECTIONNE
	$delete_photo=$bdd->query("DELETE FROM photo WHERE id=".$_GET['id']);
	//REDIRECTION VERS LA PAGE macollection.php QUI CORRESPOND A LA PAGE OU L'UTILISATEUR CONNECTES VOIT TOUTES SES PHOTOS
	header("Location: mesalbums.php?id=".$_SESSION['id']);
}	
?>