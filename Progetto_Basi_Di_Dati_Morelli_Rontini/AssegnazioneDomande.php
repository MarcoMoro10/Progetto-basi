<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <link href="standard.css" rel="stylesheet" type="text/css">
</head>
<body>

<form method="post" action="FormCreazioneDomande.php">
<h1>A quale sondaggio assegnamo le domande?</h1>
<?php
    require_once 'conn.php';
    session_start();
	if(isset($_POST['aggiungi_domanda'])){
        $mail = $_SESSION['email'];
		if($_SESSION['domain'] === "PREMIUM"){
			$query_visualizza = "SELECT * FROM SONDAGGIO,CREAZIONE_2 WHERE SONDAGGIO.Codice = CREAZIONE_2.Codice_Sondaggio AND CREAZIONE_2.Email_Premium='$mail'";
		}else if($_SESSION['domain'] === "AZIENDA"){
			$codice_azienda = $_SESSION['codice'];
			$query_visualizza = "SELECT * FROM SONDAGGIO,CREAZIONE_1 WHERE SONDAGGIO.Codice = CREAZIONE_1.Codice_Sondaggio AND CREAZIONE_1.Codice_Fiscale_Azienda='$codice_azienda'";
		}
		$stmt_visualizza = $conn->query($query_visualizza);
		while($row_visualizza = $stmt_visualizza->fetch(PDO::FETCH_ASSOC)) {
			$domanda_visualizza[] = $row_visualizza;
		}
		echo '<table>';
			echo '<thead>';
			echo	'<tr>';
			echo		'<th>Codice</th>';
			echo		'<th>Titolo</th>';
			echo		'<th>Data_Apertura</th>';
			echo		'<th>Data_Chiusura</th>';
			echo		'<th>Max_Utenti</th>';
			echo		'<th>Stato</th>';
			echo		'<th></th>';
			echo	'</tr>';
			echo '</thead>';
		
			echo'<tbody>';
		if(isset($domanda_visualizza)){
			foreach($domanda_visualizza as $codice_sondaggi) :
				echo'<tr>';
				$input_id =1; 
				echo "<td for= '".$input_id."'>" .$codice_sondaggi['Codice'].'</td>';
				echo "<td for= '".$input_id."'>" .$codice_sondaggi['Titolo'].'</td>';
				echo "<td for= '".$input_id."'>" .$codice_sondaggi['Data_Apertura'].'</td>';
				echo "<td for= '".$input_id."'>" .$codice_sondaggi['Data_Chiusura'].'</td>';
				echo "<td for= '".$input_id."'>" .$codice_sondaggi['Max_Utenti'].'</td>';
				echo "<td for= '".$input_id."'>" .$codice_sondaggi['Stato'].'</td>';
				echo"<td>" ;
				echo'<input type="submit" id="Codice" name="Codice" value="'.$codice_sondaggi['Codice'].'">'; //<?php echo($codice_sondaggi['Codice']); 
				echo"</td>"; 
				echo'</tr>';
			endforeach;
		}
    }
?>
</form>
<form method="post" action="Sondaggi.php">
    <input type="submit" id="back" name="back" value="Torna a Sondaggi"> 
</form>
</body>
    </html>