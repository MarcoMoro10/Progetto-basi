<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link href="prova.css" rel="stylesheet" type="text/css">

</head>
<body>
<form action="FormCreazioneSondaggio.php" method="post"> 
<h1>CREAZIONE DEI SONDAGGIO</h1>
<label for="codice_del_sondaggio">Codice:</label>
  <input type="codice_del_sondaggio" name="codice_del_sondaggio" id="codice_del_sondaggio">

<legend>Stato:</legend>
    <select name="stato" id="stato">
    <option value="APERTO" selected="selected">APERTO </option>
    <option value="CHIUSO">CHIUSO </option>
    </select>

  <label for="titolo">Titolo:</label>
  <input type="titolo" name="titolo" id="titolo">

  <label for="data_A"> Data Apertura:</label>
  <input type="date" name="data_A" id="data_A">

  <label for="data_C">Data Chiusura:</label>
  <input type="date" name="data_C" id="data_C">
  <br>
  <label for="categoria">Dominio Parola:</label>
 <?php 
        require_once 'conn.php';
        require_once 'connMongo.php';
        session_start();
        error_reporting(0);
        $categoria= filter_input(INPUT_POST,"categoria", FILTER_SANITIZE_STRING);
		$query="SELECT Parola FROM DOMINIO";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		

			echo('  <select name="categoria" id="categoria"> ');
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				
				echo "<option value='".$row["Parola"]."'>".$row["Parola"]." </option>";
			}
		echo('</select>');
        ?>
    <br>
  <label for="numeroUtenti">Massimo Numero Utenti:</label>
  <input type="numeroUtenti" name="numeroUtenti" id="numeroUtenti">
  <input type="submit" name="CreazioneSondaggio" value="Crea Sondaggio">

  <?php

    if( isset($_POST["codice_del_sondaggio"]) && isset($_POST["stato"]) && isset($_POST["categoria"])&& isset($_POST["titolo"])&& isset($_POST["numeroUtenti"])){
        $stato=$_POST["stato"];
        $categoria=$_POST["categoria"];
        $titolo=$_POST['titolo'];
        $Utenti=$_POST['numeroUtenti'];
        $Data_C=$_POST['data_C'];
        $Data_A=$_POST['data_A'];
        $dominio=$_SESSION['domain'];
        $_codice = $_POST["codice_del_sondaggio"];
        
        $query_check="SELECT * FROM SONDAGGIO WHERE Codice=:codice_sondaggio";
        $stmt_check = $conn->prepare($query_check);
        $stmt_check->bindParam(':codice_sondaggio',$_codice);
        $stmt_check->execute();
        if($stmt_check ->rowCount()>0){
          echo "<script>alert('Codice già presente in database');</script>";
        }else{
          $_SESSION['code'] = $_codice;
        }
        
        if($dominio === "PREMIUM" and !empty($stato) and !empty($categoria) and !empty($titolo) and !empty($Utenti) and !empty($Data_A) and !empty($Data_C) and !empty($_codice)){
            $mail=$_SESSION['email'];
            $current_timestamp = time();
            if($Data_C < $Data_A || strtotime($_POST['data_A']) < $current_timestamp){
              echo "<script>alert('Le date non vanno bene');</script>";
            }else{
              $query = "CALL Inserisci_SONDAGGIO_PREMIUM (?,?, ?, ?, ?, ?, ?,?)" ;
              $stmt = $conn->prepare($query);
              $document = [
                "messaggio" => "Creazione Sondaggio Premium",
                "chi" => "$mail",
                "nome" => "$titolo",
                "date" => new MongoDB\BSON\UTCDateTime()
              ];
              $bulk = new MongoDB\Driver\BulkWrite;
              $bulk->insert($document);
              
              $m->executeBulkWrite("$dbname.$collection", $bulk);
              // Impostazione dei parametri in input
              $stmt->bindParam(1, $_codice, PDO::PARAM_STR);
              $stmt->bindParam(2, $stato, PDO::PARAM_STR);
              $stmt->bindParam(3, $titolo, PDO::PARAM_STR);
              $stmt->bindParam(4, $Data_C, PDO::PARAM_STR);
              $stmt->bindParam(5, $Data_A, PDO::PARAM_STR);
              $stmt->bindParam(6, $Utenti, PDO::PARAM_STR);
              $stmt->bindParam(7, $categoria, PDO::PARAM_STR);
              $stmt->bindParam(8, $mail, PDO::PARAM_STR);

              // Esecuzione della stored procedure
              $stmt->execute();
              header("Location: Sondaggi.php");
              $conn = null;
            }
        }else if($dominio === "AZIENDA" and !empty($stato) and !empty($categoria) and !empty($titolo) and !empty($Utenti) and !empty($Data_A) and !empty($Data_C) and !empty($_codice)){
          $codice_azienda=$_SESSION['codice'];
          $current_timestamp = time();
            if($Data_C < $Data_A || strtotime($_POST['data_A']) < $current_timestamp){
              echo "<script>alert('Le date non vanno bene');</script>";
            }else{
              $query = "CALL Inserisci_SONDAGGIO_AZIENDA (?,?, ?, ?, ?, ?, ?,?)" ;
              $stmt = $conn->prepare($query);
              // Impostazione dei parametri in input            
              $stmt->bindParam(1, $_codice, PDO::PARAM_STR);
              $stmt->bindParam(2, $stato, PDO::PARAM_STR);
              $stmt->bindParam(3, $titolo, PDO::PARAM_STR);
              $stmt->bindParam(4, $Data_C, PDO::PARAM_STR);
              $stmt->bindParam(5, $Data_A, PDO::PARAM_STR);
              $stmt->bindParam(6, $Utenti, PDO::PARAM_STR);
              $stmt->bindParam(7, $categoria, PDO::PARAM_STR);
              $stmt->bindParam(8, $codice_azienda, PDO::PARAM_STR);
              // Esecuzione della stored procedure 
              $stmt->execute();
              $document = [
                "messaggio" => "Creazione Sondaggio Azienda",
                "chi" => "$codice_azienda",
                "nome" => "$titolo",
                "date" => new MongoDB\BSON\UTCDateTime()
              ];
              $bulk = new MongoDB\Driver\BulkWrite;
              $bulk->insert($document);
              $m->executeBulkWrite("$dbname.$collection", $bulk);
              echo("Inserito");
              header("Location: Sondaggi.php");
              $conn = null;
            }
        }else{
          echo "<script>alert('Alcuni campi sono vuoti');</script>";
        }

      }else{
      }
  ?>
</form>
    <form method="post" action = "Sondaggi.php">
    <input type="submit" name="back" value="Ritorna a sondaggi">
    </form>
</body>
</hmtl>