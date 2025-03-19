<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <link href="standard.css" rel="stylesheet" type="text/css">
</head>
<body>
<form action="InserisciPREMIO.php" method="post">
<h1>SCEGLIERE IL PREMIO</h1>
      
  <?php	
  	error_reporting(0);			
    session_start();
    require_once 'conn.php';
    require_once 'connMongo.php';
    if(isset($_POST['premio'])){
      ?>
        <h1>QUALE PREMIO VUOI ASSEGNARE?</h1>
        <?php
          $query = "SELECT * FROM PREMI_DISPONIBILI";
          $stmt = $conn->query($query);
          while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $utenti_premi[] = $row;
          }
          echo '<table>';
          echo '<thead>';
          echo	'<tr>';
          echo		'<th>Nome</th>';
          echo		'<th></th>';
          echo	'</tr>';
          echo '</thead>';
          echo'<tbody>';
          foreach($utenti_premi as $codice) :
            echo'<tr>';
            $input_id =1; 
            echo "<td for= '".$input_id."'>" .$codice['Nome'].'</td>';
                  echo"<td>" ;
            echo'<input type="submit" id="utente" name="utente" value="'.$codice['Codice'].'">'; //<?php echo($codice_sondaggi['Codice']); 
            echo"</td>";
            echo'</tr>';
          endforeach;	
     }
    
    if(isset($_POST['utente'])){
      ?>
        <h1>A CHI VUOI ASSEGNARE IL PREMIO?</h1>
        <?php
         $_SESSION['premio']=$_POST['utente'];
         $codice_premio = $_SESSION['premio'];
         $query = "SELECT Minimo_Punti FROM PREMI_DISPONIBILI WHERE Codice = '$codice_premio'";
         $stmt = $conn->query($query);
         // Prendi il risultato della query e mettilo in una variabile
         $risultato = $stmt->fetchColumn();
        $query = "SELECT * FROM UTENTE WHERE Totale_Bonus >= '$risultato' AND Email NOT IN(SELECT Email_Utente FROM VINCITA WHERE Codice_Premio ='$codice_premio')";
        $stmt = $conn->query($query);
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$utenti[] = $row;
		}
		echo '<table>';
		echo '<thead>';
		echo	'<tr>';
		echo		'<th>Email Utente</th>';
		echo		'<th></th>';
		echo	'</tr>';
		echo '</thead>';
		echo'<tbody>';
		foreach($utenti as $codice) :
			echo'<tr>';
			$input_id =1; 
			echo "<td for= '".$input_id."'>" .$codice['Email'].'</td>';
            echo"<td>" ;
			echo'<input type="submit" id="manda" name="manda" value="'.$codice['Email'].'">'; //<?php echo($codice_sondaggi['Codice']); 
			echo"</td>"; 
			echo'</tr>';
		endforeach;	
    }else{
    }
    if(isset($_POST['manda'])){
        $query = "CALL Inserisci_VINCITA_PREMI(?, ?)";        
        $stmt = $conn->prepare($query);
        $codice_premio = $_SESSION['premio'];
        $mail_utente_interna=$_POST['manda'];
        $codice = rand();
        $stmt->bindParam(1, $mail_utente_interna, PDO::PARAM_STR);
        $stmt->bindParam(2, $codice_premio, PDO::PARAM_STR);
        $stmt->execute();
        $document = [
          "messaggio" => "Assegno Nuovo Premio",
          "da chi" => "$email_ammi",
          "a chi" => "$mail_utente_interna",
          "nome" => "$descrizione",
          "date" => new MongoDB\BSON\UTCDateTime()
        ];
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->insert($document);
        $m->executeBulkWrite("$dbname.$collection", $bulk);
    
        header("Location: Sondaggi.php");
      }
    ?>
</form>
<form method="post" action="Sondaggi.php">
  <input type="submit" id="back" name="back" value='indietro'>
</form>
</body>
</html>
