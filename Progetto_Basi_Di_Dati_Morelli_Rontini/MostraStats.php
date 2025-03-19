<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <link href="standard.css" rel="stylesheet" type="text/css">
</head>
<body>
<form action = "viewStatistiche.php" method="post">
	<h1>SCEGLIERE IL SONDAGGIO</h1>
<?php
session_start();
error_reporting(0);
require_once 'conn.php';

if($_SESSION['domain']==="PREMIUM"){
    if(isset($_POST['stats'])){
        $mail=$_SESSION['email'];
        $query_visualizza_sondaggi = "SELECT * FROM SONDAGGIO,CREAZIONE_2 WHERE SONDAGGIO.Codice = CREAZIONE_2.Codice_Sondaggio AND CREAZIONE_2.Email_Premium='$mail'";
		
		$stmt_visualizza_sondaggi = $conn->query($query_visualizza_sondaggi);
		while($row_visualizza_sondaggi = $stmt_visualizza_sondaggi->fetch(PDO::FETCH_ASSOC)) {
			$domanda_visualizza_sondaggi[] = $row_visualizza_sondaggi;
		}
		echo '<table>';
			echo '<thead>';
			echo	'<tr>';
			echo		'<th>Titolo</th>';
			echo		'<th></th>';
			echo	'</tr>';
			echo '</thead>';
		
			echo'<tbody>';
		foreach($domanda_visualizza_sondaggi as $codice_view) :
			echo'<tr>';
			$input_id =1; 
			echo "<td for= '".$input_id."'>" .$codice_view['Titolo'].'</td>';
			echo"<td>" ;
			echo'<input type="submit" id="visualizza_statistiche_sondaggio" name="visualizza_statistiche_sondaggio" value="'.$codice_view['Codice'].'">'; //<?php echo($codice_sondaggi['Codice']); 
			echo"</td>"; 
			echo'</tr>';
        endforeach;
    }


}else if($_SESSION['domain']==="AZIENDA"){
    if(isset($_POST['stats'])){
        $codice_azienda=$_SESSION['codice'];
        $query_visualizza_sondaggi = "SELECT * FROM SONDAGGIO,CREAZIONE_1 WHERE SONDAGGIO.Codice = CREAZIONE_1.Codice_Sondaggio AND CREAZIONE_1.Codice_Fiscale_Azienda='$codice_azienda'";
		
		$stmt_visualizza_sondaggi = $conn->query($query_visualizza_sondaggi);
		while($row_visualizza_sondaggi = $stmt_visualizza_sondaggi->fetch(PDO::FETCH_ASSOC)) {
			$domanda_visualizza_sondaggi[] = $row_visualizza_sondaggi;
		}
		echo '<table>';
			echo '<thead>';
			echo	'<tr>';
			echo		'<th>Titolo</th>';
			echo		'<th></th>';
			echo	'</tr>';
			echo '</thead>';
		
			echo'<tbody>';
		foreach($domanda_visualizza_sondaggi as $codice_view) :
			echo'<tr>';
			$input_id =1; 
			echo "<td for= '".$input_id."'>" .$codice_view['Titolo'].'</td>';
			echo"<td>" ;
			echo'<input type="submit" id="visualizza_statistiche_sondaggio" name="visualizza_statistiche_sondaggio" value="'.$codice_view['Codice'].'">'; //<?php echo($codice_sondaggi['Codice']); 
			echo"</td>"; 
			echo'</tr>';
        endforeach;
    }


    
}
    ?>
	</form>
	<form method="post" action = "Sondaggi.php">
		<input type="submit" id="back" name="back" value='indietro'>
</form>
    </body>
    </html>