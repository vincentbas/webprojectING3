<?php

session_start();
include_once('cookie_connect.php');
//Connexion base de données
$bdd = new PDO('mysql:host=localhost;dbname=phplogin', 'root', '');


if(isset($_GET['id']) AND $_GET['id'] > 0) 
{
$getid = intval($_GET['id']);
$requser = $bdd->prepare("SELECT * FROM users WHERE id ='$getid'");
$requser->execute(array($getid));
$userinfo = $requser->fetch();	

$photos = $bdd->query("SELECT * FROM photo WHERE parametre='Public' ORDER BY id DESC");
$nb_photos=$photos->rowCount();

$like = $bdd->query('SELECT * FROM aime');	
?>
<!DOCTYPE html>
<html>

<head>
    <title> AMSTRAGRAM </title>
    <meta charset="utf-8"/>
    <link rel="stylesheet" href="monfil.css"/>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
	<script type="text/javascript" src="zoombox/zoombox.js"></script>
	<link href="zoombox/zoombox.css" rel="stylesheet" type="text/css" media="screen" />
  

	<script type="text/javascript">
	jQuery(function($)
	{
		$('a.zoombox').zoombox();
	});
	</script> 
	<script type="text/javascript">
		<!--Fonction JavaScript qui permet d'afficher et de centrer une fenêtre pop-up-->
			function open_infos()
			{
				width = 700;
				height = 600;
				if(window.innerWidth)
				{
					var left = (window.innerWidth-width)/2;
					var top = (window.innerHeight-height)/2;
				}
				else
				{
					var left = (document.body.clientWidth-width)/2;
					var top = (document.body.clientHeight-height)/2;
				}
				window.open('exif.php','nom_de_ma_popup','menubar=no, scrollbars=no, top='+top+', left='+left+', width='+width+', height='+height+'');
			}
	</script>
</head>

<body>

	<!-- barre du haut, curieusement il n'y a que sur cette page que le hover fonctionne. Je vais regler ca -->
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
			<form method="POST" action="">
				<input type="text" name="q" id="rechercher" placeholder="rechercher"/>
				<input type="submit" value="OK"/>
			</form>	
			</li>
			
			<li>
				<a href="deconnexion.php">deconnexion</a>
			</li>
		</ul>
	</nav>
	<ul id="mylist">
		<?php 
		if(!empty($userinfo['avatar']))
		{
			?>
			<img src="users/avatar/<?php echo $userinfo['avatar'];?>" style="width:150px;height:150px;"/>
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

	</ul>
	
	<img id="ap1" src="images/others/ap1.png" style="width:100px;height:100px;"/>
	<img id="ap2" src="images/others/ap2.png"style="width:100px;height:100px;"/>
	<img id="ap3" src="images/others/ap3.png"style="width:100px;height:100px;"/>
	<img id="ap4" src="images/others/ap4.png"style="width:100px;height:100px;"/>
	<img id="ap5" src="images/others/ap5.png"style="width:100px;height:100px;"/>
	
	<div class="listphotos">
		<div id="parallaxe">
			<?php  
			//FONCTION RECHERCHE OP
			//Elle fonctionne mais elle n'affiche pas encore les résultats correspondants à la recherche
			if(isset($_POST['q']))
			{
				$motcle = $_POST['q'];
				$resultat = $bdd->query("SELECT DISTINCT P.id, P.NomImage, P.img, P.description, P.lieu, P.date, P.parametre FROM photo P, users U WHERE P.parametre = 'Public' AND (P.NomImage LIKE '%$motcle%' OR (P.proprio = U.id AND U.pseudo LIKE '%$motcle%') OR (P.lieu LIKE '%$motcle%'))");
				$nb_resultat = $resultat->rowCount();
			}
			else 
			{
				$resultat = $bdd->query("SELECT * FROM photo WHERE parametre = 'Public' ORDER BY date DESC");
				$nb_resultat = $resultat->rowCount();
			}
			?> <br/><br/><br/></br></br> <?php

			if ($nb_resultat == 0) 
			{
				if(isset($_POST['q']))
				{
				?>
				<h2> Aucune photo ne correspond à votre recherche pour : <?php echo $motcle ?> </h2>
				<?php
				}
				else 
				{
					?>
				<h2> Nous n'avons pas de photos a vous montrer! </h2>
				<?php
				}
			}
			else if(isset($_POST['q']))
			{
			?>
				<h2> Il y a <?php echo $nb_resultat ?> résultat(s) correspondant a votre recherche : <?php echo $motcle ?></h2>
			<?php

			}

			//FONCTION LIKE ET UNLIKE
			if (isset($_POST['jaime']))
			{	
				$id_photo = $_POST['idphoto'];
				$id_user = $userinfo['id'];							
				$result = $bdd->prepare('INSERT INTO aime (id_photo,id_user) VALUES(?,?)');
				$result->execute(array($id_photo, $id_user));
			}	
			if (isset($_POST['unlike']))
			{
					$id_photo = $_POST['idphoto'];
					$id_user = $userinfo['id'];
					$result = $bdd->query("DELETE FROM aime WHERE id_photo = '$id_photo' AND id_user = '$id_user'");	
			}
			?> <br/><br/><br/></br></br> <?php
			
			//FONCTION FOLLOW ET UNFOLLOW
			if (isset($_POST['suivre']))
			{	
				$id_user1 = $getid;
				$id_user2 = $_POST['iduser2'];							
				$result = $bdd->prepare('INSERT INTO follower (id_user1,id_user2) VALUES(?,?)');
				$result->execute(array($id_user1, $id_user2));
			}	
			if (isset($_POST['unfollow']))
			{
					$id_user1 = $getid;
					$id_user2 = $_POST['iduser2'];	
					$result = $bdd->query("DELETE FROM follower WHERE id_user1 = '$id_user1' AND id_user2 = '$id_user2'");	
			}
			
			//FONCTION COMMENTAIRES
			if(isset($_POST['commentaire']))
			{
					$id_user = $getid;
					$id_photo = $_POST['idphoto'];
					$message = $_POST['commentaire'];
					$result = $bdd->prepare('INSERT INTO comments (id_user, id_photo, contenu) VALUES (?, ?, ?)');
					$result->execute(array($id_user, $id_photo, $message));
			}
			
					// On affiche chaque entrée une à une
					while ($photos_data = $photos->fetch())
					{		
						$cheminM = "photos/min/".$photos_data['img'];
						$cheminG = "photos/".$photos_data['img'];
					?>
					<div id="parallaxe-inner">
					<article class="pt">
					<div id="gallerie">				
					<a class="zoombox zgallery1" href="<?php echo $cheminG;?>"> 
					<?php 
						if(!empty($photos_data['img']))
						{
					?>
					<img id="photos" src="<?php echo $cheminM ?>" style="width:350px;height:350px;"/>
					<?php						
						}
					?>
						</a> 
						<div id="text1">
							<h4>
							
								Nom Image : <?php echo $photos_data['NomImage']; ?><br />
								Pseudo: <?php 
											$req = $bdd->query("SELECT pseudo FROM users WHERE id = '$photos_data[proprio]'");
											$pseudo = $req->fetch();
											echo $pseudo['pseudo']; 
										?><br />
								Date: <?php echo $photos_data['date']; ?><br />
								Heure : <?php echo $photos_data['heure']; ?><br/>
								Lieu : <?php echo $photos_data['lieu']; ?><br/>
								Description : <?php echo $photos_data['description']; ?><br/><br/>

								<form method="post" action ="">

									<input type="hidden"  name="idphoto"  value="<?php echo $photos_data['id'] ?>">
									<?php
									$photolike = $bdd->query("SELECT * FROM aime WHERE id_photo = '$photos_data[id]' AND id_user = '$getid'"); 
									$photolikenum = $photolike->rowCount();
									if($photolikenum == 0)
									{
									?>
										<input type="submit" name="jaime" id="Like" value="Like">
									<?php
									}
									else 
									{
									?>
										<input type="submit" name="unlike" id="UnLike" value="UnLike"><?php
									}
									?>
									
									<input type="hidden"  name="iduser2"  value="<?php echo $photos_data['proprio'] ?>">
									<?php
									$user_follow = $bdd->query("SELECT * FROM follower WHERE id_user1 = '$userinfo[id]' AND id_user2 = '$photos_data[proprio]'"); 
									$nb_user_follow = $user_follow->rowCount();
									if($nb_user_follow == 0)
									{
									?>
										<input type="submit" name="suivre" id="follow" value="Follow">
									<?php
									}
									else 
									{
									?>
										<input type="submit" name="unfollow" id="unfollow" value="UnFollow"><?php
									}
									?>
									
									<input type="hidden"  name="exif_photo"  value="<?php echo $photos_data['id'] ?>">
									<input type="submit" name="exif" id="Exif" value="Exif" onclick="javascript:open_infos();">
								</form>
								
								<div id="com">
								<?php 
									$nb_comments = $bdd->query("SELECT * FROM comments WHERE id_photo= '$photos_data[id]'");
									$nb_result = $nb_comments->rowCount();
								?>
								Nb: <?php echo $nb_result ?>
								<form method="POST" action ="">
									
									<input type="hidden"  name="idphoto"  value="<?php echo $photos_data['id']?>">
									<input type="textarea" name="commentaire" id="comment" placeholder="commentez...">
								</form>

									<table>
									<?php 
										for($i =0; $i < $nb_result; $i++){
											$comment = $nb_comments->fetch();
											$requete = $bdd->query("SELECT pseudo FROM users WHERE id = '$comment[1]'");
											$pseudo = $requete->fetch();
											?>
												<tr>	
													<td>
													<?php echo $pseudo['pseudo'] ?> :
													</td>
													<td>
													<?php echo $comment['contenu'] ?>
													</td>
												</tr>
											<?php
										}
									?>
									</table>

								
							</div>
						
							</h4>
							
						</div>
					</div>
					</article>
					</div>
				<?php
				}			
			?>	
		
	</div>
</body>

</html>
<?php
}
else
{
header("Location: monfil.php?id=".$_SESSION['id']);
}
?>