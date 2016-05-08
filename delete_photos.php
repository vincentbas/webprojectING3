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
	
	//REDIRECTION VERS LA PAGE admin_photos.php QUI CORRESPOND A LA PAGE OU L'ADMINISTRATEUR VOIT TOUTES LES PHOTOS PUBLIEES SUR LE SITE
	header("Location: admin_photos.php?id=".$_SESSION['id']);
}	
?>
