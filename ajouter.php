<?php
//RECUPERATION DES VARIABLES DE SESSION
session_start();
include_once('cookie_connect.php');	

try
{
	//CONNEXION BDD
	$bdd = new PDO('mysql:host=localhost;dbname=phplogin', 'root', '');
}
catch(Exception $e)
{
	die('Erreur : '.$e->getMessage());
}

//TEST: VERIFICATION QUE LA VARIABLE ID EXISTE ET SUPERIEUR A 0
if(isset($_GET['id']) AND $_GET['id'] > 0)
{
	//SECURISER LA VARIABLE CONVERSION DE CE MET L'UTILISATEUR EN NOMBRE
	$getid = intval($_GET['id']);
	
	//REQUETE: SELECTIONNE LES INFORMATIONS DE L'UTILISATEUR CONNECTE
	$requser = $bdd->prepare("SELECT * FROM users WHERE id ='$getid'");
	$requser->execute(array($getid));
	
	//(fetch()): FONCTION QUI RECUPERE LES INFOS PROPPRE A L'UTILISATEUR CONNECTE
	$userinfo = $requser->fetch();	
?>

<!DOCTYPE html>
<html>

	<head>
		<title> AMSTRAGRAM </title>
		<meta charset="utf-8"/>
		<link rel="stylesheet" href="ajouter.css"/>
	</head>


	<body>
		<h3> AMSTRAMGRAM </h3>
		<h6> Bour et Bour et Ratatam !</h6>
		
		<!--MENU-->
		<nav>
			<ul>
				<li>
					<a href="ajouter.php" id="here">ajouter</a>
				</li>

				<li>
					<a href="monfil.php">mon fil</a>
				</li>

				<li>
					<a href="macollection.php">ma collection</a>
				</li>

				<li>
					<a href="parametres.php">mes parametres</a>
				</li>

				<li>
				<form method="POST" action="">
					<input type="text" name="q" id="search" placeholder="rechercher"/>
					<input type="submit" value="OK" id="search_button"/>
				</form>	
				</li>
				
				<li>
					<a href="home.php">deconnexion</a>
				</li>
			</ul>
		</nav>
		
		<!--CHOIX ENTRE LA PAGE AJOUTER UNE IMAGE OU UN ALBUM-->
		<div id="choice">
			<ul>
				<li id="choice1">
					<a href="image.php?id=.$_SESSION['id']">Ajouter une Photo</a>
				</li>

				<li id="choice2">
					<a href="album.php?id=.$_SESSION['id']">Ajouter un Album</a>
				</li>
			</ul>
		</div>
	</body>
</html>
<?php
}
else
{
header("Location:ajouter.php?id=".$_SESSION['id']);
}
?>