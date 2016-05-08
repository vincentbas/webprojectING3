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

	//REQUETE: SELECTIONNE DANS L'ORDRE DECROISSANT TOUTES LES PHOTOS OU LE PROPRIETAIRE DE LA PHOTO CORRESPOND A L'UTILISATEUR CONNECTE
	$photos = $bdd->query("SELECT * FROM photo WHERE proprio='$getid'  ORDER BY id DESC");	
?>

<!DOCTYPE html>
<html>

	<head>
		<title> AMSTRAGRAM </title>
		<meta charset="utf-8"/>
		<link rel="stylesheet" href="macollection.css"/>
	</head>


	<body>
		<!--barre du haut-->

		<h3> AMSTRAMGRAM </h3>
		<h6> Bour et Bour et Ratatam !</h6>
		
		<!--MENU-->
		<nav>
			<ul>
				<li>
					<a href="ajouter.php">ajouter</a>
				</li>

				<li>
					<a href="monfil.php">mon fil</a>
				</li>

				<li>
					<a href="macollection.php" id="here">ma collection</a>
				</li>

				<li>
					<a href="parametres.php">mes parametres</a>
				</li>

				<li>
					<form method="POST" action="">
						<input type="search" name="q" placeholder="rechercher" id="search"/>
						<input type="submit" value="OK" id="search_button"/>
					</form>
				</li>
				
				<li>
					<a href="home.php">deconnexion</a>
				</li>

			</ul>
		</nav>
		
		<!--CHOIX ENTRE LA PAGE MES PHOTOS OU MES ALBUMS-->
		<div id="choice">
			<ul>
				<li id="choice1">
					<a href="mesimages.php">Mes Photos</a>
				</li>

				<li id="choice2">
					<a href="mesalbums.php">Mes Albums</a>
				</li>
			</ul>
		</div>		
	</body>
</html>
<?php
}
else
{
	header("Location: macollection.php?id=".$_SESSION['id']);
}
?>