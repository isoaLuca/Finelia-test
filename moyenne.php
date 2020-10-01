<!DOCTYPE html>
<html>
	<head>
		<title> Liste des étudiants </title>
		<meta charset="utf-8">
	</head>
	<body>
	<?php 
		include ('base.php');
		$students    = $db->query('SELECT * FROM etudiant');	
			
		function stdAvg($etudiantId) {
			include ('base.php');
			$StdNote = $db->prepare('SELECT * FROM note WHERE etudiant_id = ?');
			$StdNote->execute([$etudiantId]);
			$reslt = $StdNote->fetchAll();
			$sum = 0;
			$div = 0;
			$avg = NULL;
			foreach ($reslt as $value) {
				if (count($reslt) == 1) {
					$sum = $value['note'];
				} else {
					$sum += $value['note']*$value['coefficient'];
					$div += $value['coefficient'];
				}
			}

			if (count($reslt) > 0) {
				if (count($reslt) == 1) {
					$avg = $sum;
				} else {
					$avg = $sum / $div;
				}
			}
			
			$req = $db->prepare('UPDATE etudiant SET moyenne = ? WHERE id = ?');
			$req->execute(array($avg,$etudiantId));
			return $avg;
		}

		function getEtudiantName($etudiantId) {
			include ('base.php');
			$StdName = $db->prepare('SELECT * FROM etudiant WHERE id = ? ORDER BY id');
			$StdName->execute([$etudiantId]);
			return $StdName->fetch();
		}

		function getNomMatiere($matiereId) {
			include ('base.php');
			$sql = $db->prepare('SELECT nom FROM matiere WHERE id = ?');
			$sql->execute(array($matiereId));

			$reslt = $sql->fetch();
			return $reslt[0];
		}

		function getNotes($etudiantId) {
			include ('base.php');
			$matierelist = $db->query('SELECT * FROM matiere');	
			$StdNote = $db->prepare('SELECT * FROM note WHERE etudiant_id = ? ORDER BY matiere_id');
			$StdNote->execute([$etudiantId]);
			$etudiant = getEtudiantName($etudiantId);
			$tabBulletin = [
				"nom" => NULL,
				"prenom" => NULL
			];

			foreach ($matierelist as $matiere) {
				$tabBulletin[$matiere['nom']] = NULL;
			}
			$tabBulletin['moyenne'] = NULL;
			$tabBulletin['nom'] = $etudiant['nom'];
			$tabBulletin['prenom'] = $etudiant['prenom'];
			foreach ($StdNote->fetchAll() as $value) {
				$tabBulletin[getNomMatiere($value['matiere_id'])] = $value['note'];
			}
			$tabBulletin['moyenne'] = stdAvg($etudiantId);
			$matierelist->closeCursor();
			return $tabBulletin;
		}


		$Bulletins = []; 
		while ($student = $students->fetch()) :
			array_push($Bulletins, getNotes($student['id']));
		endwhile;?>

		<h2>Bulletin :</h2>
		<table>
			<th>
				<?php foreach ($Bulletins[1] as $column => $value): ?>
					<td><?php echo $column?></td>
				<?php endforeach ?>
			</th>
				<?php foreach ($Bulletins as $value): ?>
					<tr>
						<td></td>
						<?php foreach ($value as $elt) { ?>
							<td> <?php echo $elt;?> </td>
						<?php } ?>
					</tr>
				<?php endforeach ?>
		</table>


	<?php
	//var_dump($Bulletins);
		$students->closeCursor(); 
	?>
	</body>
</html>