<?php
session_start();
//Connexion base de données
$bdd = new PDO('mysql:host=localhost;dbname=phplogin', 'root', '');
include_once('cookie_connect.php');	
if(isset($_GET['id']) AND $_GET['id'] > 0) 
{
$getid = intval($_GET['id']);
$requser = $bdd->prepare("SELECT * FROM users WHERE id ='$getid'");
$requser->execute(array($getid));
$userinfo = $requser->fetch();
?>
<!DOCTYPE html>
<html>

<head>
    <title> AMSTRAGRAM </title>
    <meta charset="utf-8"/>
    <link rel="stylesheet" href="admin.css"/>
</head>

<body>

	<!-- barre du haut, curieusement il n'y a que sur cette page que le hover fonctionne. Je vais regler ca -->
	<h3> AMSTRAMGRAM </h3>
	<h6> Espace Administrateur</h6>
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
			<?php echo $userinfo['pseudo'];?>
		</li>
		<li>
			<?php echo $userinfo['date_naissance'];?>
		</li>
		<li>
			<?php echo $userinfo['pays'];?>
		</li>
	</ul>
	<!-- liste des 3 photos-->
	<!-- chaque element (article) est constitué d'une photo, d'un sous titre (h4), et d'une légenre (p)-->
	<!--le fond noir, c'est juste pour me reperer-->

	<div class="listphotos">
		
					
					<?php
					$users = $bdd->query("SELECT id, avatar, pseudo, date_naissance, pays FROM users");

					// On affiche chaque entrée une à une
					while ($users_data = $users->fetch())
					{
					?>
					<article class="pt">
					
					<h4>
					<br/>
						<?php 
						if(!empty($users_data['avatar']))
						{
							?>
							<img src="users/avatar/<?php echo $users_data['avatar'];?>" style="width:100px;height:100px;"/><br/>
							<?php
						}
						?>
						Pseudo : <?php echo $users_data['pseudo']; ?><br />
						Date de naissance : <?php echo $users_data['date_naissance']; ?><br />
						Pays : <?php echo $users_data['pays']; ?><br/>

						<div class="row">
							<a href="delete_users.php?id=<?= $users_data['id']?>">Suppression Utilisateur</a>
						</div>
						<br/></h4>
						</article>
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
header("Location: admin_users.php?id=".$_SESSION['id']);
}
?>