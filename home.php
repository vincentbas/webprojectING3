<?php
//DEMARRAGE DE LA SESSION
session_start();

include_once('cookie_connect.php');
try
{
	//CONNEXION BDD
	$bdd = new PDO('mysql:host=localhost;dbname=phplogin', 'root', '');
	
	//VERIFICATION QUE L'UTILISATEUR A APPUYE SUR LE BOUTON CONNEXION
	if(isset($_POST['connexion'])) 
	{
		//SECURISATION HACHAGE ET SIMPLIFICATION DES VARIABLES (htmlspecialchars)-> FONCTION QUI EVITE INJECTIONS DE CODE, CONVERTIT LES CARACTERES SPECIAUX EN ENTITES HTML
	    $pseudoconnect = htmlspecialchars($_POST['pseudoconnect']);
	    $passwordconnect = sha1($_POST['passwordconnect']);
		
		//VERIFICATION QUE TOUS LES CHAMPS DU FORMULAIRE CONNEXION SONT REMPLIS
	    if(!empty($pseudoconnect) AND !empty($passwordconnect)) 
	    {	
			//REQUETE: SELECTIONNE TOUS LES UTILISATEURS DE LA TABLE USERS OU LE MDP ET LE PSEUDO CORRESPONDENT A CEUX RENTRES PAR L'UTILSATEUR
			$connectmbr = $bdd->prepare("SELECT * FROM users WHERE pseudo = ? AND password = ?");
			$connectmbr->execute(array($pseudoconnect, $passwordconnect));
			
			//(rowCount):FONCTION QUI RENVOIE LE NOMBRE DE MAIL TROUVE
			$userexist = $connectmbr->rowCount();
			
			//TEST: SI VARIABLE $userexist = 1, L'UTILISATEUR EXISTE
			if($userexist == 1) 
			{
				if(isset($_POST['remember']))
				{
					setcookie('pseudo',$pseudoconnect,time()+365*24*3600,null,null,false,true);
					setcookie('password',$passxordoconnect,time()+365*24*3600,null,null,false,true);
				}
				
				//(fetch()): FONCTION QUI RECUPERE LES INFOS DE LA REQUETE STOCKE DANS LA VARIABLE $connectmbr PDO
				$userinfo = $connectmbr->fetch();
				
				//VARIABLES DE SESSION
				$_SESSION['id'] = $userinfo['id'];
				$_SESSION['pseudo'] = $userinfo['pseudo'];
				$_SESSION['date_naissance'] = $userinfo['date_naissance'];
				$_SESSION['pays'] = $userinfo['pays'];
				$_SESSION['email'] = $userinfo['email'];
				$_SESSION['avatar'] = $userinfo['avatar'];
				$_SESSION['rang'] = $userinfo['rang'];
				
				//TEST: SI LE RANG VAUT 1, L'UTILISATEUR EST REDIRIGE VERS LA PAGE admin.php QUI CORRESPOND A L'ESPACE ADMINISTRATEUR
				if($userinfo['rang']==1)
				{
					header("Location: admin.php?id=".$_SESSION['id']); 
				}
				//TEST: SI LE RANG VAUT 0, L'UTILISATEUR EST REDIRIGE VERS LA PAGE monfil.php QUI CORRESPOND A L'ESPACE MEMBRE
				elseif($userinfo['rang']==0)
				{
					header("Location: monfil.php?id=".$_SESSION['id']);
				}
				//SINON L'UTILISATEUR EST REDIRIGE VERS LA PAGE home.php QUI CORRESPOND A LA PAGE DE CONNEXION/INSCRIPTION
				else
				{
					header("Location: home.php");
				}
			} 
			else  
			{
				$erreur = "Mauvais mail ou mot de passe !";
			}
	    } 
	    else 
	    {
			$erreur = "Tous les champs doivent être complétés !";
	    }
	}
	
	//VERIFICATION QUE L'UTILISATEUR A APPUYE SUR LE BOUTON INSCRIPTION
	if (isset($_POST['inscription'])) 
	{
		//SECURISATION ET SIMPLIFICATION DES VARIABLES (htmlspecialchars)-> FONCTION QUI EVITE INJECTIONS DE CODE, CONVERTIT LES CARACTERES SPECIAUX EN ENTITES HTML
	    $pseudo = htmlspecialchars($_POST['pseudo']);
	    $date_naissance=htmlspecialchars(trim($_POST['date_naissance']));
	    $pays=htmlspecialchars(trim($_POST['pays']));
	    $email = htmlspecialchars($_POST['email']);
	    $confirme_mail = htmlspecialchars($_POST['confirme_mail']);
	   
	    //CRYPTAGE DU MOT DE PASSE(sha1)
	    $password = sha1($_POST['password']);
	    $confirme_mdp = sha1($_POST['confirme_mdp']);
	   
	    //VERIFICATION QUE TOUS LES CHAMPS DU FORMULAIRE INSCRIPTION SONT REMPLIS
	    if(!empty($_POST['pseudo']) AND !empty($_POST['date_naissance']) AND !empty($_POST['pays']) AND !empty($_POST['email']) AND !empty($_POST['confirme_mail']) AND !empty($_POST['password']) AND !empty($_POST['confirme_mdp'])) 
	    {
		    //(strlen):FONCTION QUI RENVOIE LE NOMBRE DE CARACTERES, LA TAILLE DE LA CHAINE
		    $pseudolength = strlen($pseudo);
		  
		    //TEST: MOT DE PASSE DOIT ETRE SUPERIEUR A 7 CARACTERES
		    if(strlen($password)>7) 
		    {
				//TEST: LES VARIABLES E-mail EY CONFIRME_MAIL SONT IDENTIQUES
				if($email == $confirme_mail) 
				{
					if(filter_var($email, FILTER_VALIDATE_EMAIL)) 
					{
					     //REQUETE: SELECTIONNE TOUS LES E-mail DE LA TABLE USERS
					     $reqmail = $bdd->prepare("SELECT * FROM users WHERE email = ?");
					     $reqmail->execute(array($email));
				   
					     //(rowCount):FONCTION QUI RENVOIE LE NOMBRE DE MAIL TROUVE
					     $mailexist = $reqmail->rowCount();
				   
					     //TEST: VERIFICATION QUE LE MAIL N'EXISTE PAS
					     if($mailexist == 0) 
					    {
						    //TEST: VERIFICATION QUE LE MDP ET LE CONFIRME-MDP SONT IDENTIQUES
						    if($password == $confirme_mdp) 
						    {
							 //REQUETE: INSERTION DES VARIABLES ENTREES PAR L'UTILISATEUR DANS LA TABLE USERS
							 $insertmbr = $bdd->prepare("INSERT INTO users(pseudo, date_naissance, pays, email, password, avatar,rang) VALUES(?, ?, ?, ?, ?, ?, ?)");
							 $insertmbr->execute(array($pseudo, $date_naissance, $pays, $email, $password, "default.jpg","0"));
							 
							 $compte = "Votre compte a bien été créé";
							 echo '<font color="blue">'.$compte."</font>";  
							} 
							else 
							{
								$erreur = "Veuillez saisir le même mot de passe";
							}
					    } 
					    else 
					    {
							$erreur = "Adresse mail déjà utilisée !";
					    }
					} 
					else 
					{
					   $erreur = "Votre adresse email n'est pas valide !";
					}
				} 
				else 
				{
					$erreur = "Vos adresses mail ne correspondent pas !";
				}
			} 
			else 
			{
			 $erreur = "Votre pseudo doit comporter 7 caractères minimum !";
			}
	    } 
	    else 
		{
		  $erreur = "Tous les champs doivent être complétés !";
		}
	}
}
catch(Exception $e)
{
	//MESSAGE D'ERREUR PUIS SUPPRESSION DE LA PAGE - EQUIVALENT A exit()
	die('Erreur : '.$e->getMessage());
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title> AMSTRAGRAM </title>
		<meta charset="utf-8"/>
		<link rel="stylesheet"  href="home.css"/>
		<div id="particles-js"></div>
		<!-- SCRIPTS -->
		<script src="js/particles.js"></script>
		<script src="js/app.js"></script>
	</head>
	
	<body>
		<h1> AMSTRAMGRAM </h1>
		<h2> PIC ET PIC ET COLLEGRAM !</h2>
		<ul id="maliste">
		
			<li id="connexion" class="list">
				<h3>
					Connexion
				</h3>
				<ul>
					<!-- FORMULAIRE DE CONNEXION -->
					<form method="POST" action="">
						<li>
							<label for="pseudo">Votre pseudo</label></br>
							<input type="text" name="pseudoconnect" value=""/></br>							
						</li>
						<li>
							<label for="password">Votre password</label></br>
							<input type="password" name="passwordconnect" value=""/></br>	
						</li>
						<li>
						<label for="valider">Connexion</label></br>
							<input type="submit" name="connexion" value="GO!" id="go"></input>
							<input type="reset" name="reset" id="Reinitialiser" value="Reinitialiser" id="renitialiser" ></code></br></br>
							<input type="checkbox" name="remember" id="remembercheckbox"/><label for="remembercheckbox">Se souvenir de moi</label></br>	
						</li>
					</form>
				</ul>
			</li>
			
			
			<li id="inscription" class="list">
				<h3>
					Inscription
				</h3>
				<ul id="maliste2">
				
					<!--FORMULAIRE INSCRIPTION-->
					<form method="POST" action="">
						
						<li>
							<label for="pseudo">Votre pseudo</label></br>
							<input type="text" value="" id="pseudo" name="pseudo" value="<?php if(isset($pseudo)) { echo $pseudo; } ?>"/></br></br>
						</li>
						<li>
							<label for="password">Votre password</label></br>
							<input type="password" value="" id="password" name="password"/></br></br>
						</li>
						<li>
							<label for="confirme_mdp">Confirmation</label></br>
							<input type="password" value="" id="confirme_mdp" name="confirme_mdp"/></br></br>
						</li>
						<li>
							<label for="date_naissance">Votre date de naissance</label></br>
							<input type="date" value="" id="date_naissance" name="date_naissance" value="<?php if(isset($date_naissance)) { echo $date_naissance; } ?>"/></br></br>
						</li>
						<li>
							<label for="pays">Votre pays</label></br>
							<select name="pays" id="pays">
								<option value="France">France</option>
								<option value="Espagne">Espagne</option>
								<option value="Italie">Italie</option>
								<option value="Royaume-Uni">Royaume-Uni</option>
								<option value="Canada">Canada</option>
								<option value="Etats-unis">Etats-Unis</option>
								<option value="Chine">Chine</option>
								<option value="Japon">Japon</option>
							</select></br></br>
						</li>
						<li>
							<label for="email">Votre email</label></br>
							<input type="email" value="" id="email"name="email" value="<?php if(isset($email)) { echo $email; } ?>" /></br></br>
						</li>
						<li>
							<label for="confirme_mail">Confirmation</label></br>
							<input type="email" value="" id="confirme_mail" name="confirme_mail" value="<?php if(isset($confirme_mail)) { echo $confirme_mail; } ?>"/></br></br>
						</li>
						<li>
							<label for="go">Inscription</label></br>
							<input type="submit" name="inscription" value="GO!" id="go"/>
							<input type="reset" name="reset" id="Reinitialiser" value="Reinitialiser"id="renitialiser" ></code>
						</li>
					</form>
				</ul>	
			</li>
		</ul>
		
		<!--AFFICHAGE DES ERREURS FORMULAIRE-->
		<?php
		  if(isset($erreur)) 
		  {
				echo '<font color="red">'.$erreur."</font>";        
		  }
		?>		
	</body>
</html>