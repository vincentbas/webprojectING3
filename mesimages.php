<?php
//RECUPERATION DES VARIABLES DE SESSION
session_start();

//CONNEXION BDD
$bdd = new PDO('mysql:host=localhost;dbname=phplogin', 'root', '');
include_once('cookie_connect.php');

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

	$photos = $bdd->query("SELECT * FROM photo WHERE proprio='$getid'  ORDER BY id DESC");	
?>

<!DOCTYPE html>
<html>

	<head>
		<title> AMSTRAGRAM </title>
		<meta charset="utf-8"/>
		<link rel="stylesheet" href="macollectionphotos.css"/>
		
		<!--SCRIPT ET CSS DE LA LIGHTBOX Zoombox-->
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
		<script type="text/javascript" src="zoombox/zoombox.js"></script>
		<link href="zoombox/zoombox.css" rel="stylesheet" type="text/css" media="screen" />
		<script type="text/javascript">
		jQuery(function($)
		{
			$('a.zoombox').zoombox();
		});
		</script> 
	</head>
	
	<body>
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
					<input type="text" name="q" id="search" placeholder="rechercher"/>
					<input type="submit" value="OK" id="search_button"/>
				</form>	
				</li>

				<li>
					<a href="home.php">deconnexion</a>
				</li>
			</ul>
		</nav>

		<!--PROFIL: INFORMATION PROPRE A L'UTILISATEUR-->
		<ul id="mylist">
			</br>
			<?php 
			if(!empty($userinfo['avatar']))
			{
				?>
				<!--AVATAR UTILISATEUR-->
				<img src="users/avatar/<?php echo $userinfo['avatar'];?>" style="width:150px;height:150px;border-radius:5px;" border="white" />
				<?php
			}
			?>
			<li>
				Espace membre
			</li>
			<li>
				<?php echo $userinfo['pseudo'];?>
			</li>
			<li>
				<?php echo $userinfo['date_naissance'];?>
			</li>
			<li>
				<?php echo $userinfo['pays'];?>
			</li>
			</br>
			
			<?php
			//GESTION DES BOUTONS RADIOS ET SELECTION DES PHOTOS SELON LE PARAMETRE DE LA PHOTO
			if(isset($_POST['envoi']))
			{
				$bouton_statut=$_POST['bouton'];
				if( $bouton_statut=='Privee'OR $bouton_statut=='Public')
				{
					//REQUETE: SELECTION DE TOUTES LES PHOTOS DE L'UTILISATEUR DE LA SESSION SELON LA VISIBILITE DE LA PHOTO CHOISIE
					$photos = $bdd->query("SELECT * FROM photo WHERE proprio='$getid' and parametre='$bouton_statut' ORDER BY id DESC");
					$bouton_statut='Combo';
				}
				else
				{
					//REQUETE: SELECTION DE TOUTES LES PHOTOS DE L'UTILISATEUR DE LA SESSION 
					$photos = $bdd->query("SELECT * FROM photo WHERE proprio='$getid'  ORDER BY id DESC");
					$bouton_statut='Combo';
				}
			}	
			?>
					
			<!--DIFFERENTS BOUTONS PARAMETRES PHOTOS-->
			<form action="" method="post" enctype="multipart/form-data">
				<input type="radio" name="bouton" value="Public"> Public
				</br>
				<input type="radio" name="bouton" value="Privee"> Privee
				</br>
				<input type="radio" name="bouton" value="Combo" > Combo
				</br>
				<input type="submit" name="envoi" id="Importer" value="GO"></code>
			</form>
			</br>
		</ul>
		
		
		<?php
		// ON AFFICHE LES ENTREES UNE A UNE
		while ($photos_data = $photos->fetch())
		{
			//ADRESSE DES PHOTOS MINIATURES
			$cheminM = "photos/min/".$photos_data['img'];
			//ADRESSE DES PHOTOS 
			$cheminG = "photos/".$photos_data['img'];
		?>
		<!-- LA BALISE ARTICLE CONTIENT LA PHOTO, LES DIFFERENTS BOUTONS, ET LES INFOS DE LA PHOTO-->
		<article class="pt">
			<!--LIEN VERS LA LIGHTBOX Zoombox-->
			<a class="zoombox zgallery2" href="<?php echo $cheminG;?>"> 
			<?php 
				if(!empty($photos_data['img']))
				{
			?>	<!--IMAGE DES ALBUMS PUBLIEE-->				
				<img id="photos" src="<?php echo $cheminM ?>" style="width:350px;height:350px;"/>
			<?php
				}
			?>
			</a>
			<div id="text">
			<h4>
				@<?php 
					//RECUPERATION ET AFFICHAGE DU PSEUDO DE LA PERSONNE QUI A PUBLIE LA PHOTO EN PASSANT PAR SON ID
					$req = $bdd->query("SELECT pseudo FROM users WHERE id = '$photos_data[proprio]'");
					$pseudo = $req->fetch();
					echo $pseudo['pseudo']; 
				?><br />
			</h4>
			<p id="legende">
				<?php echo $photos_data['description']; ?><br />
			</p>
			<p id="infos">
			<?php
				//REQUETE:SELECTIONNE TOUS LES LIKES QUI CORRESPOND A L'ID DE LA PHOTO
				$like = $bdd->query("SELECT * FROM aime WHERE id_photo = '$photos_data[id]'"); 
				//NOMBRE DE RESULTAT CORRESPONDANT A LA REQUETE CI-DESSUS
				$nb_like = $like->rowCount();
				//AFFICHAGE DU NOMBRE DE LIKES
				echo $nb_like." Likes";
			?>
			</br>
			<?php 
				//REQUETE: SELECTION DE TOUS LES COMMENTAIRES DE LA PHOTO
				$nb_comments = $bdd->query("SELECT * FROM comments WHERE id_photo= '$photos_data[id]'");
				//NOMBRE DE RESULTAT CORRESPONDANT A LA REQUETE CI-DESSUS
				$nb_result = $nb_comments->rowCount(); 
				//AFFICHAGE DU NOMBRE DE COMMENTAIRES
				echo $nb_result." Commentaires" ;
			?>			
			</p>
			<!--LIEN PERMETTANT DE SUPPRIMER UNE PHOTO-->
			<a href="supprime_photos.php?id=<?= $photos_data['id']?>">
				<img src="images/others/supprimer.png" width="100px;"/>
			</a>
			<a href=""><img src="images/others/modifier.png" width="100px;"/></a>	 
			</div>	
		</article>
		<?php
		}
		?>
	</body>
</html>
<?php
}
else
{
	header("Location: mesimages.php?id=".$_SESSION['id']);
}
?>