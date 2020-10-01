<?php 
	$user = 'root';

	// SOUS WINDOWS
	$pass = '';

	// SOUS MAC
	//$pass = 'root';

	try {
		$db = new PDO('mysql:host=localhost;dbname=examen', $user, $pass);
	} catch (PDOException $e) {
		print "Erreur !: " . $e->getMessage() . "<br/>";
		die();
	}
?>