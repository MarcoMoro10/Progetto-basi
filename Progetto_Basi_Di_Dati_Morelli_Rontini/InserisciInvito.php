<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <link href="standard.css" rel="stylesheet" type="text/css">
</head>
<body>
<form action="AssegnaInvito.php" method="post">
  <?php	
    session_start();
    require_once 'conn.php';
		?>
        <h1>SCEGLIERE IL SONDAGGIO PER L'INVITO</h1>
      <?php
	  if($_SESSION['domain']==="PREMIUM"){
      	$premium=$_SESSION['email'];
        $query = "SELECT * FROM SONDAGGIO WHERE Codice IN (SELECT Codice_Sondaggio FROM CREAZIONE_2 WHERE Email_Premium=:email_premium)";
        $stmt = $conn->prepare($query);
				$stmt->bindParam(':email_premium', $premium);
				$stmt->execute();
        $sondaggi_creati = array();
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$sondaggi_creati[] = $row;
				}
        echo '<table>';
			echo '<thead>';
			echo	'<tr>';
			echo		'<th>Titolo</th>';
			echo		'<th></th>';
			echo	'</tr>';
			echo '</thead>';
		
			echo'<tbody>';
      	foreach($sondaggi_creati as $index=>$codice) {
				echo'<tr>';
				 $input_id =($index + 1); 
				echo "<td for= '".$input_id."'>" .$codice['Titolo'].'</td>';
				echo"<td>" ;
				echo'<input type="submit" id="Codice_Sondaggi" name="Codice_Sondaggi" value="'.$codice['Codice'].'">'; 
				echo"</td>";  
				 echo'</tr>';
				}
			echo'</tbody>';
		echo'</table>';
	} else if($_SESSION['domain']=== "AZIENDA"){
		$codice_azienda=$_SESSION['codice'];
        $query = "SELECT * FROM SONDAGGIO WHERE Codice IN (SELECT Codice_Sondaggio FROM CREAZIONE_1 WHERE Codice_Fiscale_Azienda=:codice_azienda) ";
        $stmt = $conn->prepare($query);
				$stmt->bindParam(':codice_azienda', $codice_azienda);
				$stmt->execute();
        $sondaggi_creati = array();
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$sondaggi_creati[] = $row;
				}
        echo '<table>';
			echo '<thead>';
			echo	'<tr>';
			echo		'<th>Titolo</th>';
			echo		'<th></th>';
			echo	'</tr>';
			echo '</thead>';
		
			echo'<tbody>';
      	foreach($sondaggi_creati as $index=>$codice) {
				echo'<tr>';
				 $input_id =($index + 1); 
				echo "<td for= '".$input_id."'>" .$codice['Titolo'].'</td>';
				echo"<td>" ;
				echo'<input type="submit" id="Codice_Sondaggi" name="Codice_Sondaggi" value="'.$codice['Codice'].'">'; 
				echo"</td>";  
				 echo'</tr>';
				}
			echo'</tbody>';
		echo'</table>';
		}
      ?>

</form>
<form method="post" action="Sondaggi.php">
        <input type="submit" id="menu" name="menu" value="Torna al menu">
    </form>
</body>
</html>
