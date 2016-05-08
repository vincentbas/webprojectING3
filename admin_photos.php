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
?>
<!DOCTYPE html>
<html>

	<head>
		<title> AMSTRAGRAM </title>
		<meta charset="utf-8"/>
		<link rel="stylesheet" href="admin_users.css"/>
	</head>

	<body>
		<h3> AMSTRAMGRAM </h3>
		<h6> Espace Administrateur</h6>
		
		<!--MENU-->
		<nav>
			<ul>
				<li>
					<a href="admin.php">informations</a>
				</li>
				<li>
					<a href="admin_users.php">utilisateurs</a>
				</li>
				<li>
					<a href="admin_photos.php">photos</a>
				</li>
				<li>
					<input type="text" placeholder="rechercher"/>
					<input type="submit" value="OK"/>
				</li>
				<li>
					<a href="deconnexion.php">deconnexion</a>
				</li>
			</ul>
		</nav>
		
		<!--PROFIL: INFORMATION PROPRE A L'ADMINISTRATEUR-->
		<ul id="mylist">
		</br>
			<?php 
			if(!empty($userinfo['avatar']))
			{
				?>
				<!--AVATAR ADMINISTRATEUR-->
				<img src="users/avatar/<?php echo $userinfo['avatar'];?>" style="width:150px;height:150px;"/>
				<?php
			}
			?>
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
		</ul>
		<div class="listphotos">	
			<table>		
				<?php
				//REQUETE: SELECTION DE TOUTES LES PHOTOS DE LA TABLE PHOTO
				$photos = $bdd->query("SELECT * FROM photo");
				$nb_result=$photos->rowCount();
				
				// ON AFFICHE LES ENTREES UNE A UNE
				while ($photos_data = $photos->fetch())
				{
				?>
				
				<article class="pt">				
					<h4>
					<br/>
						<?php 
						if(!empty($photos_data['img']))
						{
							?>
							<!--AFFICHAGE IMAGE-->
							<img src="photos/<?php echo $photos_data['img'];?>" style="width:100px;height:100px;"/><br/>
							<?php
						}
						?>
						<!--AFFICHAGE INFORMATIONS CONCERNANT LA PHOTO-->
						Nom Image : <?php echo $photos_data['NomImage']; ?><br />
						Date: <?php echo $photos_data['date']; ?><br />
						Heure : <?php echo $photos_data['heure']; ?><br/>
						Lieu : <?php echo $photos_data['lieu']; ?><br/>
						Description : <?php echo $photos_data['description']; ?><br/></br>
						
						<!--LIEN SUPPRESSION PHOTOS-->
						<div class="row">
							<a href="delete_photos.php?id=<?= $photos_data['id']?>"><img src="images/others/supprimer.png" width="100px;"/></a>
						</div>
					<br/>
					</h4>			
				</article>
				<?php
				}
			?>
			</table>
		</div>
	</body>
</html>
<?php
}
else
{
header("Location: admin_photos.php?id=".$_SESSION['id']);
}
?>