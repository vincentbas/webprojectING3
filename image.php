<?php
//RECUPERATION DES VARIABLES DE SESSION
session_start();
include_once('cookie_connect.php');	

try
{
	//CONNEXION BDD
	$bdd = new PDO('mysql:host=localhost;dbname=phplogin;charset=utf8', 'root', '');
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
	
	//VERIFICATION QUE L'UTILISATEUR A APPUYE SUR LE BOUTON IMPORTER
	if(isset($_POST['importer']))
	{	
		//SECURISATION SIMPLIFICATION DES VARIABLES (htmlspecialchars)-> FONCTION QUI EVITE INJECTIONS DE CODE, CONVERTIT LES CARACTERES SPECIAUX EN ENTITES HTML
		$NomImage=htmlspecialchars(trim($_POST['NomImage']));
		$date=htmlspecialchars(trim($_POST['date']));
		$heure=htmlspecialchars(trim($_POST['heure']));
		$lieu=htmlspecialchars(trim($_POST['lieu']));
		$description=htmlspecialchars(trim($_POST['description']));
		$parametre=htmlspecialchars(trim($_POST['parametre']));
		$album=htmlspecialchars(trim($_POST['choix_album']));
		
		//TEST: VERIFICATION QUE L'UTILISATEUR DE LA SESSION A AJOUTE UN FICHIER
		if(!empty($_FILES))
			{
				// FONCTION PRESENT DANS LE FICHIER imgClass
				require("imgClass.php");
				
				$img=$_FILES['img'];
				//(strtolower()): RENVOIE UNE CHAINE EN MINUSCULE (substr()):RETOURNE UN SEGMENT DE CHAINE
				$ext= strtolower(substr($img['name'],-3));
				$allow_ext=array('jpg','png','gif', 'jpeg');
				
				if(in_array($ext,$allow_ext))
				{
					//DEPLACEMENT DU FICHIER TAMPON DANS SA NOUVELLE DESTINATION
					move_uploaded_file($img['tmp_name'],"photos/".$img['name']); 
					//ADRESSE DE L'IMAGE
					$Chemin = $img['name'];
					//APPEL DES FONCTIONS PRESENTES DANS LE FICHIER imgClass.php
					Img::creerMin("photos/".$img['name'],"photos/min",$img['name'],350,350);
					Img::convertirJPG("photos/".$img['name']); 
				}
				
				else
				{
					$erreur ="Votre fichier n'est pas une image"; 
				}
			}
			
		//TEST: VERIFICATION QUE TOUS LES CHAMPS SONT REMPLIS
		if($NomImage&&$date&&$heure&&$lieu&&$description&&$img&&$parametre)
		{		
			
			//REQUETE D'INSERTION DANS LA TABLE PHOTO
			$req = $bdd->prepare('INSERT INTO photo (NomImage,proprio,date,heure,lieu,description,parametre,img, album_id) VALUES(?,?,?,?,?,?,?,?,?)');
			$req->execute(array($_POST['NomImage'], $getid, $_POST['date'], $_POST['heure'],$_POST['lieu'],$_POST['description'],$_POST['parametre'],$img['name'],$_POST['choix_album']));
			
			//TEST: SI LA REQUETE RENVOIE TRUE
			if($req)
				{
					//REDIRECTION VERS LA monfil.php QUI CORRESPOND A LA PAGE ACTUALITE DU SITE 
					header('Location: monfil.php?id='.$_SESSION['id']);
					echo "Fichier uploadé";
					exit;
				}
				//SINON ON RENVOIE A LA PAGE DE CONNEXION/INSCRIPTION	
				else
				{
					header('Location: home.php');	
				}
		}
		else
		{
			$erreur = "Remplissez tous les champs";
		}
	}
?>
<!DOCTYPE html>
<html>

	<head>
		<title> AMSTRAGRAM </title>
		<meta charset="utf-8"/>
		<link rel="stylesheet" href="ajouterphoto.css"/> 
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
					<a href="deconnexion.php">deconnexion</a>
				</li>
			</ul>
		</nav>


		<div id="ajout">
			
			<?php
			//AFFICHAGE DES MESSAGES D'ERREURS DUS AUX FORMULAIRE D'AJOUT DE PHOTOS
			if(isset($erreur))
			{
				echo $erreur;
			}
			?>
			<!--FORMULAIRE AJOUTER UNE PHOTO-->
			<form method="POST" action="" enctype="multipart/form-data">
				</br>
				<li>chargez votre image</li>		
					<input type="file" id="file" name="img"/></br></br>
				<li>choix album</li>		
				<li>
					<select name="choix_album" id="Album">
					<?php
					//SELECTION DES ALBUMS QUI ONT ETE CREE PAR L'UTILISATEUR DE LA SESSION OU QUI ONT LE NOM DEFAULT
					$query=$bdd->query("SELECT* FROM albums WHERE id_user= '$getid' OR Nom='default'");
					//AFFICHAGE DES NOMS DES ALBUMS
					while($album_data=$query->fetch())
					{
						$album_id=$album_data['id'];
						$album_name=$album_data['Nom'];
						echo"<option value='$album_id'>$album_name</option>";	
					}
						
					?> 
					</select></br></br>
				</li>
				<li>Nom de l'image</li>
					<input type="text" name="NomImage" id="Nom_Fichier"/></br></br>
				<li>Date</li>
					<input type="date" name="date" id="Date"/></br></br>
				<li>Heure</li>
					<input type="time" name="heure" id="Heure"/></br></br>
				<li>Lieu</li>
					<input type="text" name="lieu" id="Lieu" /></br></br>
				<li>Confidentialité</li>
					<select name="parametre" id="Parametre">
						<option value="Public">Public</option>
						<option value="Privee">Privee</option>
					</select></br></br>
				<li>Description</li>
					<textarea name="description" id="Description" rows="2" cols="20" ></textarea></br></br>
					<input type="submit" name="importer" id="Importer" value="GO"></code>
					<input type="reset" name="réinitialiser" id="Reinitialiser" value="Reinitialiser" id="Reinitialiser"></code>	</br>
			</form>
		</div>
	</body>
</html>
<?php
}
else
{
header("Location:image.php?id=".$_SESSION['id']);
}
?>