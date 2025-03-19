<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link href="domande.css" rel="stylesheet" type="text/css">

</head>
<body>
<form method="post">
    <table>
			<thead>
				<tr>
					<th>Id</th>
					<th>Testo</th>
					<th>Punteggio</th>
					<th>Foto</th>
				</tr>
			</thead>
			<tbody>
				<?php
                session_start();
                error_reporting(0);
                if (isset($_POST['Codice'])) {
                    $codice = $_POST['Codice'];
                    $_SESSION['matteo'] = $codice;
                }else{
                    $codice=$_SESSION['matteo'];
                }
                require_once 'conn.php';
                require_once 'connMongo.php';
                $email_utente=$_SESSION['email'];
				// query per recuperare i dati dal database
				$query_domanda = "SELECT * FROM DOMANDA,POSSESSO WHERE Codice_Sondaggio='$codice' AND Domanda.Id=POSSESSO.Id_Domanda AND 
                DOMANDA.Id NOT IN (SELECT Id_Domanda 
                                    FROM RISPOSTA
                                    WHERE Email_Utente = '$email_utente')";
				$stmt_domanda = $conn->query($query_domanda);
				// Cicla sui dati e crea una riga della tabella per ogni elemento
				$utenti = array();
				while($row_domanda = $stmt_domanda->fetch(PDO::FETCH_ASSOC)) {
					$domanda[] = $row_domanda;
				}
                $query_aperta = "SELECT * FROM DOMANDA,POSSESSO,APERTA WHERE Codice_Sondaggio='$codice' AND DOMANDA.Id=POSSESSO.Id_Domanda AND DOMANDA.id=APERTA.id AND 
                DOMANDA.Id NOT IN (SELECT Id_Domanda 
                                    FROM RISPOSTA
                                    WHERE Email_Utente = '$email_utente')";
                $stmt_aperta = $conn->query($query_aperta);
                while($row_aperta = $stmt_aperta->fetch(PDO::FETCH_ASSOC)) {
                    $domanda_aperta[] = $row_aperta;
                }
                $query_chiusa = "SELECT * FROM DOMANDA,POSSESSO,CHIUSA WHERE Codice_Sondaggio='$codice' AND DOMANDA.Id=POSSESSO.Id_Domanda AND DOMANDA.id=CHIUSA.id AND 
                DOMANDA.Id NOT IN (SELECT Id_Domanda 
                                    FROM RISPOSTA
                                    WHERE Email_Utente = '$email_utente')";
                $stmt_chiusa = $conn->query($query_chiusa);
                while($row_chiusa = $stmt_chiusa->fetch(PDO::FETCH_ASSOC)) {
                    $domanda_chiusa[] = $row_chiusa;
                }

                $query_opzioni = "SELECT CHIUSA.Id,OPZIONE.Testo FROM CHIUSA,OPZIONE WHERE CHIUSA.Id = OPZIONE.Id";
                $stmt_opzioni = $conn->query($query_opzioni);
                while($row_opzioni = $stmt_opzioni->fetch(PDO::FETCH_ASSOC)) {
                    $domanda_opzioni[] = $row_opzioni;
                }
				?>
				<?php foreach($domanda_aperta as $codice) : ?>
				<tr>
				<td for="<?php echo $input_id; ?>"><?php echo $codice['Id'];?></td>
				<td for="<?php echo $input_id; ?>"><?php echo $codice['Testo'];?>
                <label for="risposta">Risposta:</label>
                <input type="text" id="<?php echo($codice["Id"]);?>" name="<?php echo($codice["Id"]);?>">
                 <input type="submit" name="Invia_Risposte_Aperta" value="<?php echo($codice["Id"]);?>">
                            <?php
                        if(isset($_POST["Invia_Risposte_Aperta"]) and isset($_POST[$codice['Id']])){
                                $domanda_finale = $_POST["Invia_Risposte_Aperta"]; 
                                $testo = $_POST[$codice['Id']]; 
                                $prova = $codice['Id'];
                                if($domanda_finale == $prova){ 
                                    $query_risp = "CALL Inserisci_RISPOSTA_APERTA(?, ?, ?, ?, ?)";
                                    $stmt = $conn->prepare($query_risp);
                                    // Impostazione dei parametri in input
                                    $codice_risposta_aperta = rand();
                                    $max_caratteri=$codice['Max_Caratteri'];
                                    $stmt->bindParam(1, $codice_risposta_aperta, PDO::PARAM_STR);
                                    $stmt->bindParam(2, $testo, PDO::PARAM_STR);
                                    $stmt->bindParam(3, $email_utente, PDO::PARAM_STR);
                                    $stmt->bindParam(4, $domanda_finale, PDO::PARAM_STR);
                                    $stmt->bindParam(5, $max_caratteri, PDO::PARAM_STR);
                                    // Esecuzione della stored procedure
                                    if(empty($testo)){
                                    }else{
                                        $stmt->execute();
                                        $document = [
                                            "messaggio" => "Risposta Alla Domanda Aperta",
                                            "chi" => "$email_utente",
                                            "domanda_id" => "$domanda_finale",
                                            "nome" => "$testo",
                                            "date" => new MongoDB\BSON\UTCDateTime()
                                        ];
                                        $bulk = new MongoDB\Driver\BulkWrite;
                                        $bulk->insert($document);
                                        $m->executeBulkWrite("$dbname.$collection", $bulk);
                                        header("Refresh: 0");
                                    }
                            }else{
                                echo("qua");
                            }
                        }
                        
                        
              
                ?>
                </td>
				<td for="<?php echo $input_id; ?>"><?php echo $codice['Punteggio'];?></td>
				<td for="<?php echo $input_id; ?>"><img src="<?php echo($codice['Foto']);?>"/></td>
                <?php endforeach;?> </tr>


                <?php foreach($domanda_chiusa as $codice) : ?>
				<tr>
				<td for="<?php echo $input_id; ?>"><?php echo $codice['Id'];?></td>
				<td for="<?php echo $input_id; ?>"><?php echo $codice['Testo'];?>
                <?php    
                foreach($domanda_opzioni as $codice_opzione) :
                    echo("<br>");
                                    if($codice['Id'] === $codice_opzione['Id']){
                                        $value = $codice_opzione['Testo'];
                                        ?>
                                        <input type="radio" name="<?php echo($codice["Id"]);?>" value="<?php echo($codice_opzione['Testo']); ?>" id="bottoneee">
                                        <label for="radio-button"><?php echo($codice_opzione['Testo']); ?></label><br>
                                        <?php
                                    }
                                endforeach;
                            ?><input type="submit" name="Invia_Risposte_Chiusa" value="<?php echo($codice["Id"]);?>"><?php
                        
                        if(isset($_POST["Invia_Risposte_Chiusa"]) and isset($_POST[$codice['Id']])){
                            $domanda_finale = $_POST["Invia_Risposte_Chiusa"]; 
                            $testo = $_POST[$codice['Id']]; 
                            $prova = $codice['Id'];
                            if($domanda_finale == $prova){
                                //devo fare la call con $domanda_finale come id domanda e $risposta come testo risposta   
                                $query_risp = "CALL Inserisci_RISPOSTA_CHIUSA(?, ?, ?, ?)";
                                $stmt = $conn->prepare($query_risp);
                                // Impostazione dei parametri in input
                                $codice_risposta_aperta = rand();
                                $email_utente=$_SESSION['email'];                            
                                $stmt->bindParam(1, $codice_risposta_aperta, PDO::PARAM_STR);
                                $stmt->bindParam(2, $testo, PDO::PARAM_STR);
                                $stmt->bindParam(3, $email_utente, PDO::PARAM_STR);
                                $stmt->bindParam(4, $domanda_finale, PDO::PARAM_STR);
                                // Esecuzione della stored procedure
                                if(empty($testo)){
                                }else{
                                    $stmt->execute();
                                    $document = [
                                        "messaggio" => "Risposta Alla Domanda Chiusa",
                                        "chi" => "$email_utente",
                                        "domanda_id" => "$domanda_finale",
                                        "nome" => "$testo",
                                        "date" => new MongoDB\BSON\UTCDateTime()
                                    ];
                                    $bulk = new MongoDB\Driver\BulkWrite;
                                    $bulk->insert($document);
                                    $m->executeBulkWrite("$dbname.$collection", $bulk);
                                    header("Refresh: 0");
                                }
                            }
                        }
                        
              
                ?>
                </td>
				<td for="<?php echo $input_id; ?>"><?php echo $codice['Punteggio'];?></td>
				<td for="<?php echo $input_id; ?>"><img src="<?php echo($codice['Foto']);?>"/></td>
                <?php endforeach;?> </tr>
			</tbody>
		</table>
  </form>
    <form method = "post" action ="Sondaggi.php">
        <input type="submit" name="Invia_Risposte" value = "Indietro" action="Sondaggi.php">                    
</from>
</body>
</html>
