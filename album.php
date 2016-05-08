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
<html>
	<head>
		<title>Album System</title>
		<meta charset="utf-8"/>
		<link rel='stylesheet' href='ajouterphoto.css'>
	</head>
	
	<body>
		<div id="ajout">
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
						<a href="deconnexion.php">deconnexion</a>
					</li>
				</ul>
			</nav>

			<h4> Création Album </h4>
			<form method="POST" action="" enctype="multipart/form-data">
			<?php
				if(isset($_POST['create']))
				{
					//SECURISATION ET SIMPLIFICATION DES VARIABLES (htmlspecialchars)-> FONCTION QUI EVITE INJECTIONS DE CODE, CONVERTIT LES CARACTERES SPECIAUX EN ENTITES HTML
					$name=htmlspecialchars(trim($_POST['NomAlbum']));
					$user_album=$getid;
					
					//TEST: VERIFICATION QUE TOUS LES CHAMPS DU FORMULAIRE CONNEXION SONT REMPLIS
					if(!empty($name))
					{
						//REQUETE: INSERTION DE L'ALBUM DANS LA TABLE ALBUMS
						$album=$bdd->prepare('INSERT INTO albums (Nom,id_user)VALUES(?,?)');
						$album->execute(array($name, $getid));
						
						//TEST: VERIFICATION QUE LA REQUETE RENVOIE TRUE
						if($album)
						{
							//REDIRECTION VERS LA PAGE monfil.php QUI CORRESPOND A LA PAGE ACTUALITE DU SITE
							header('Location: monfil.php?id='.$_SESSION['id']);
							echo "Album crée";
							exit;
						}
					}
					else
					{
						echo "Veuillez remplir tous les champs";
					}
				}
			
			?>
				<li>Nom Album</li>
				<input type="text" name="NomAlbum" id="Nom_Album"/></br></br>
				<input type="submit" name="create" id="create" value="GO"></code>
			</form>	
		</div>
	</body>
</html>
<?php
}
else
{
header("Location:album.php?id=".$_SESSION['id']);
}
?>