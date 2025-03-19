<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <link href="standard.css" rel="stylesheet" type="text/css">
</head>
<body>
<form action = "AssegnaInvito.php" method="post">
<?php
session_start();
error_reporting(0);
require_once 'conn.php';
require_once 'connMongo.php';
        ?>
        <h1>A CHI VUOI ASSEGNARE L'INVITO?</h1>
      <?php
if($_SESSION['domain']==="PREMIUM"){


if(isset($_POST['Codice_Sondaggi']) || $_SESSION['check'] === "true"){
    $_SESSION['check'] = "false";
    $email_premium=$_SESSION['email'];
    if(isset($_POST['Codice_Sondaggi'])  ){
        $_SESSION['codice_sondaggio']=$_POST['Codice_Sondaggi'];
    }
        $query = "SELECT * from UTENTE where Email NOT IN (
            SELECT Email_Utente FROM INVITO WHERE Codice IN(SELECT Codice_invito FROM INVIATO WHERE Email_Premium=:email_premium) 
            AND Codice_Sondaggio =:codice_del_sondaggio
            ) AND Email NOT IN (SELECT Email FROM PREMIUM WHERE Email = '$email_premium') ";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email_premium', $email_premium);
        $stmt->bindParam(':codice_del_sondaggio',$_SESSION['codice_sondaggio'] );
        $stmt->execute();
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
			echo'<input type="submit" id="mail" name="mail" value="'.$codice['Email'].'">'; //<?php echo($codice_sondaggi['Codice']); 
			echo"</td>"; 
			echo'</tr>';
		endforeach;	
    }
    ?>
    <?php 
    if(isset($_POST['mail'])){
        $sondaggio=$_SESSION['codice_sondaggio'];
            $mail_utente = $_POST['mail'];
            $query = "CALL Inserisci_INVITO_PREMIUM(?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            // Impostazione dei parametri in input
            $codice = rand();
            $email_premium=$_SESSION['email'];
            $esito = "Attesa";
            $stmt->bindParam(1, $codice, PDO::PARAM_STR);
            $stmt->bindParam(2, $esito, PDO::PARAM_STR);
            $stmt->bindParam(3, $mail_utente, PDO::PARAM_STR);
            $stmt->bindParam(4, $email_premium, PDO::PARAM_STR);
            $stmt->bindParam(5, $sondaggio , PDO::PARAM_STR);
            $stmt->execute();
            $document = [
                "messaggio" => "Mando un invito da Premium",
                "chi" => "$email_premium",
                "a chi" => "$mail_utente",
                "date" => new MongoDB\BSON\UTCDateTime()
              ];
              $bulk = new MongoDB\Driver\BulkWrite;
              $bulk->insert($document);
              $m->executeBulkWrite("$dbname.$collection", $bulk);
            $_SESSION['check'] = "true";
            header("Refresh: 0");
    }
}else if($_SESSION['domain']==="AZIENDA"){
    if(isset($_POST['Codice_Sondaggi'])){
        $_SESSION['codice_sondaggio']=$_POST['Codice_Sondaggi'];
            $query = "SELECT Email from UTENTE where Email NOT IN (
            SELECT Email_Utente FROM INVITO WHERE Codice  IN
            (SELECT Codice_Spedisci_Invito FROM SPEDISCI WHERE Codice_Fiscale_Azienda=:codice_fiscale_azienda)
            AND Codice_Sondaggio=:codice_del_sondaggio
           ) AND Email IN (SELECT Email_Utente FROM INTERESSAMENTO WHERE Dominio_Parola IN (SELECT Dominio_Parola FROM SONDAGGIO WHERE Codice =:codice_del_sondaggio))";
           
           $stmt = $conn->prepare($query);
           $stmt->bindParam(':codice_fiscale_azienda', $_SESSION['codice']);
           $stmt->bindParam(':codice_del_sondaggio',$_SESSION['codice_sondaggio'] );
           $stmt->execute();
           
           while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
			$utenti[] = $row;
            
		}

        foreach($utenti as $codice) :
			
            $query = "CALL Inserisci_INVITO_AZIENDA(?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            // Impostazione dei parametri in input
            $codice_invito = rand();
            $mail_utente_azienda = $codice['Email'];
            $codice_azienda=$_SESSION['codice'];
            $codice_sondaggio_azienda=$_SESSION['codice_sondaggio'];
            $esito = "Attesa";
            if(rand(1,10) >= 5){
            $stmt->bindParam(1, $codice_invito, PDO::PARAM_STR);
            $stmt->bindParam(2, $esito, PDO::PARAM_STR);
            $stmt->bindParam(3, $mail_utente_azienda, PDO::PARAM_STR);
            $stmt->bindParam(4, $codice_azienda, PDO::PARAM_STR);
            $stmt->bindParam(5, $codice_sondaggio_azienda , PDO::PARAM_STR);
            $stmt->execute();
            $document = [
                "messaggio" => "Mando un invito da Azienda",
                "chi" => "$codice_azienda",
                "a chi" => "$mail_utente_azienda",
                "date" => new MongoDB\BSON\UTCDateTime()
              ];
              $bulk = new MongoDB\Driver\BulkWrite;
              $bulk->insert($document);
              $m->executeBulkWrite("$dbname.$collection", $bulk);
            }
        endforeach;	

    }
    header("Location: InserisciInvito.php");

}
    ?>
    </form>
    <form method="post" action="InserisciInvito.php">
        <input type="submit" id="torno sondaggi" name="torno sondaggi" value="Torna all'elenco dei tuoi sondaggi">
    </form>
    </body>
    </html>