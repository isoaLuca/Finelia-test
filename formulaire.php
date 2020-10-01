<!DOCTYPE html>
<html>
	<head>
		<title>Formulaire d'entré de note</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="style.css">
		<?php 
			include ('base.php');
			$matierelist = $db->query('SELECT * FROM matiere');
		?>
	</head>
	<body>
		<h2> Formulaire étudiant : </h2>
		<form action ="" method="POST" name="blt">
			<fieldset>
    			<legend>Etudiant</legend>
				<label> Nom : </label>
				<input type='text' name="lastname" placeholder="Nom étudiant">
				<br>

				<label> Prénom : </label>
				<input type='text' name="firstname" placeholder="Prénom étudiant">
			</fieldset>

			<fieldset>
    			<legend>Note</legend>
				<label> Matière : </label>
				<select name="matiere">
					<?php while ($matiere = $matierelist->fetch()) : ?>
						<option name='<?php echo $matiere['nom'];?>'> <?php echo $matiere['nom']; ?> </option>
					<?php endwhile; ?>
				</select>
				<br>
				<input type="number" class="matbtf" name="coeff" placeholder="Entrer le coeffecient">
				<br>
				<input type="number" class="matbtf" name="notes" placeholder="Entrer la note">

				<br><br>

				<?php
					function getMatiereId($nomMatiere) {
						include ('base.php');
						$req = $db->prepare('SELECT id FROM matiere WHERE nom = ?');
						$req->execute([$nomMatiere]);
						$rsl = $req->fetch();
						return $rsl[0];
					}

					function getEtudiantId($nomEtudiant,$prenomEtudiant) {
						include ('base.php');
						$req = $db->prepare('SELECT id FROM etudiant WHERE nom = ? AND prenom = ?');
							$req->execute([
							    $nomEtudiant,
							    $prenomEtudiant
							]);
							$rsl = $req->fetch();
							return $rsl[0];
					}

					if (isset($_POST['blt'])) { 
						if (!getEtudiantId($_POST['lastname'], $_POST['firstname'])) {
							$req = $db->prepare('INSERT INTO etudiant(nom, prenom) VALUES(:lastname,:firstname)');
							$req->execute([
							   'lastname' => $_POST['lastname'],
							   'firstname' => $_POST['firstname']
							]);
						}
						$idMatiere  = getMatiereId($_POST['matiere']);
						$idEtudiant = getEtudiantId($_POST['lastname'], $_POST['firstname']);
						$sql = 'INSERT INTO note(note,coefficient,matiere_id,etudiant_id) VALUES(?,?,?,?)';
						$req = $db->prepare($sql);
						$req->execute(array($_POST['notes'],
							$_POST['coeff'],
							$idMatiere,
							$idEtudiant));

						$matierelist->closeCursor();
						echo 'Notes ajoutées';
					}
				?>

				<input type='submit' name='blt' value="Envoyer">
			</fieldset>
		</form>
			<br><br>
			<a href="http://localhost/test/moyenne.php">Voir tout</a>
	</body>
</html>