<?php
session_start();
$bdd = new PDO('mysql:host=localhost;dbname=phplogin', 'root', '');
if (isset($_GET['id']))
{
	$delete_photo=$bdd->query("DELETE FROM photo WHERE id=".$_GET['id']);
	header("Location: macollection.php?id=".$_SESSION['id']);
}	
?>