<?php
session_start();
include_once('cookie_connect.php');	
//Connexion BDD
			try
			{
				$bdd = new PDO('mysql:host=localhost;dbname=phplogin;charset=utf8', 'root', '');
			}
			catch(Exception $e)
			{
				die('Erreur : '.$e->getMessage());
			}
if(isset($_GET['id']) AND $_GET['id'] > 0) 
{
	$getid = intval($_GET['id']);
	$requser = $bdd->prepare("SELECT * FROM users WHERE id ='$getid'");
	$requser->execute(array($getid));
	$userinfo = $requser->fetch();
	
	if(isset($_POST['importer']))
	{	
		$NomImage=htmlspecialchars(trim($_POST['NomImage']));
		$date=htmlspecialchars(trim($_POST['date']));
		$heure=htmlspecialchars(trim($_POST['heure']));
		$lieu=htmlspecialchars(trim($_POST['lieu']));
		$description=htmlspecialchars(trim($_POST['description']));
		$parametre=htmlspecialchars(trim($_POST['parametre']));
		$album=htmlspecialchars(trim($_POST['choix_album']));
		
		if(!empty($_FILES))
			{
				// fonction présent dans le fichier imgClass
				require("imgClass.php");
				$img=$_FILES['img'];
				//strtolower: Renvoie une chaîne en minuscules substr:Retourne un segment de chaîne
				$ext= strtolower(substr($img['name'],-3));
				$allow_ext=array("jpg",'png','gif');
				
				if(in_array($ext,$allow_ext))
				{
					//Déplace un fichier téléchargé
					move_uploaded_file($img['tmp_name'],"photos/".$img['name']); 
					$Chemin = $img['name'];
					//Appelle des fonctions présentes dans le fichier imgClass.php
					Img::creerMin("photos/".$img['name'],"photos/min",$img['name'],350,350);
					Img::convertirJPG("photos/".$img['name']); 
				}
				else
				{
					$erreur ="Votre fichier n'est pas une image"; 
				}
			}
			
		//Condition: Tous les champs doivent être remplis
		if($NomImage&&$date&&$heure&&$lieu&&$description&&$img&&$parametre)
		{		
			
			//Requête d'insertion dans la base de donnée photo
			$req = $bdd->prepare('INSERT INTO photo (NomImage,proprio,date,heure,lieu,description,parametre,img, album_id) VALUES(?,?,?,?,?,?,?,?,?)');
			$req->execute(array($_POST['NomImage'], $getid, $_POST['date'], $_POST['heure'],$_POST['lieu'],$_POST['description'],$_POST['parametre'],$img['name'],$_POST['choix_album']));
			
			if($req)
				{
					header('Location: monfil.php?id='.$_SESSION['id']);
					echo "Fichier uploadé";
					exit;
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
    <link rel="stylesheet" href="ajouter.css"/> 
</head>


<body>
<!--barre du haut-->
	<h3> AMSTRAMGRAM </h3>
	<h6> Bour et Bour et Ratatam !</h6>
<nav>
		<ul>
		<li>
			<a href="ajouter.php">ajouter</a>
		</li>
		
		<li>
			<a href="album.php">album</a>
		</li>

		<li>
			<a href="monfil.php">mon fil</a>
		</li>

		<li>
			<a href="macollection.php">ma collection</a>
		</li>
		
		<li>
			<a href="mesalbums.php">mes albums</a>
		</li>

		<li>
			<a href="parametres.php">mes parametres</a>
		</li>
		
		<li>
			<a href="deconnexion.php">deconnexion</a>
		</li>
	</ul>
</nav>


<div id="ajout">

	<?php
	if(isset($erreur))
	{
		echo $erreur;
	}
	?>
	</br>
	<img src="images/others/ajouter_photo.jpg" style="width:130px;height:130px;"/>
	<form method="POST" action="" enctype="multipart/form-data">
	</br>
		</br>
		<li>chargez votre image</li>		
		<input type="file" id="file" name="img"/>
		</br>
		</br>
		<li>choix album</li>		
		<select name="choix_album" id="Album">
		<?php
		$query=$bdd->query('SELECT* FROM albums');
		while($album_data=$query->fetch())
		{
			$album_id=$album_data['id'];
			$album_name=$album_data['Nom'];
			echo"<option value='$album_id'>$album_name</option>";	
		}
			
		?> 
		</select>
		</br>
		</br>
		<li>Nom de l'image</li>
		<input type="text" name="NomImage" id="Nom_Fichier"/>
		</br>
		</br>
		<li>Date</li>
		<input type="date" name="date" id="Date"/>
		</br>
		</br>
		<li>Heure</li>
		<input type="time" name="heure" id="Heure"/>
		</br>
		</br>
		<li>Lieu</li>
		<input type="text" name="lieu" id="Lieu" />
		</br>
		</br>
		<li>Confidentialité</li>
		<select name="parametre" id="Parametre">
			<option value="Public">Public</option>
			<option value="Privee">Privee</option>
		</select>
		</br>
		</br>
		<li>Description</li>
		<textarea name="description" id="Description"  ></textarea>
		</br>
		</br>
		<input type="submit" name="importer" id="Importer" value="GO"></code>
		<input type="reset" name="réinitialiser" id="Reinitialiser" value="Reinitialiser" id="Reinitialiser"></code>	
		</br>
	</form>
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