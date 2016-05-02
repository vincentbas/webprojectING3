<?php
session_start();
$bdd = new PDO('mysql:host=localhost;dbname=phplogin', 'root', '');
if (isset($_GET['id']))
{
	$delete_user=$bdd->query("DELETE FROM users WHERE id=".$_GET['id']);
	header("Location: admin_users.php?id=".$_SESSION['id']);
}	
?>
