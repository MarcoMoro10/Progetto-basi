<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link href="prova.css" rel="stylesheet" type="text/css">

</head>
<body>
<h1>CREAZIONE DELLE DOMANDE</h1>
<form method="post" action="Sondaggi.php">
    <input type="submit" name="ritorna" id="ritorna" value="Fine Domande Sondaggio">         
</form>
<form action="FormCreazioneDomande.php" method="post">
  
    <input type="submit" name="domAperta" id="domAperta" value="Crea Domanda Aperta">
    <input type="submit" name="domChiusa" id="domChiusa" value="Crea Domanda Chiusa">
<?php 
        require_once 'conn.php';
        require_once 'connMongo.php';
        session_start();
        //error_reporting(0);
        if(isset($_POST['Codice'])){
            $codice_sondaggio=$_POST['Codice'];
            $_SESSION['Codice_del_sondaggio']= $codice_sondaggio;
        }else{
            $codice_sondaggio= $_SESSION['Codice_del_sondaggio'];
        }
        
        if(isset($_POST['domAperta'])){
            ?>
            <label for="testo">Testo:</label>
            <input type="testo" name="testo" id="testo">

            <label for="punteggio"> Punteggio:</label>
            <input type="punteggio" name="punteggio" id="punteggio">

            <label for="foto">Foto:</label>
            <input type="foto" name="foto" id="foto">
        
            <label for="max_caratteri">Max Caratteri:</label>
            <input type="max_caratteri" name="max_caratteri" id="max_caratteri">

            <input type="submit" name="inviaAperta" id="inviaAperta" value="Creazione Domanda Aperta">

        <?php }
        else if(isset($_POST['domChiusa']) or isset($_POST['aggiungiOpzione'])){
            ?>
            <label for="testo">Testo:</label>
            <input type="testo" name="testo" id="testo">

            <label for="punteggio"> Punteggio:</label>
            <input type="punteggio" name="punteggio" id="punteggio">

            <label for="foto">Foto:</label>
            <input type="foto" name="foto" id="foto">
        
            <label for="opzione1">Prima Opzione:</label>
            <input type="opzione1" name="opzione1" id="opzione1">

            <label for="opzione2">Second Opzione:</label>
            <input type="opzione2" name="opzione2" id="opzione2">

            <?php
            if(isset($_POST['aggiungiOpzione']) and $_POST['opzioneadd'] != 0){
                for($i=0;$i<$_POST['opzioneadd'];$i++){
                    ?>
                    <label for="opzione">Opzioni Aggiuntive:</label>
                    <input type="opzione" name="<?php echo($i+3);?>" id="<?php echo($i+3);?>">
            <?php
                }
            }           
            ?>
            
            <label for="opzioneadd">Numero Opzioni Aggiuntive?:</label>
            <input type="opzioneadd" name="opzioneadd" id="opzioneadd">
            <input type="submit" name="aggiungiOpzione" id="aggiungiOpzione" value="Aggiungi Opzione">
            <input type="submit" name="inviaChiusa" id="inviaChiusa" value="Creazione Domanda Chiusa">
            <?php
        }else{

        }
        ?>
  <?php

    if(isset($_POST['inviaAperta'])){
        $testo=$_POST["testo"];
        $punteggio=$_POST["punteggio"];
        $foto=$_POST['foto'];
        $max_caratteri=$_POST['max_caratteri'];
        $dominio = $_SESSION['domain'];  
       
        if(!empty($testo) and !empty($punteggio)  and !empty($max_caratteri)){
            $query = "CALL Inserisci_DOMANDA_APERTA_";
            $query .= $dominio;
            $query .= "(:testo, :punteggio, :foto, :max_caratteri, :code, :codice_sondaggio, @new_id)" ;
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':testo', $testo);
            $stmt->bindParam(':punteggio', $punteggio);
            $stmt->bindParam(':foto', $foto);
            $stmt->bindParam(':max_caratteri', $max_caratteri);
            if($dominio === "AZIENDA"){
                $code = $_SESSION['codice'];
                $stmt->bindParam(':code', $code);
            }else if($dominio === "PREMIUM"){
                $code=$_SESSION['email'];
                $stmt->bindParam(':code', $code);
            }
            $stmt->bindParam(':codice_sondaggio', $codice_sondaggio);

            $stmt->execute();
            $document = [
                "messaggio" => "Creazione Domanda Aperta",
                "chi" => "$code",
                "nome" => "$testo",
                "date" => new MongoDB\BSON\UTCDateTime()
              ];
              $bulk = new MongoDB\Driver\BulkWrite;
              $bulk->insert($document);
              $m->executeBulkWrite("$dbname.$collection", $bulk);
            // Esecuzione della stored procedure   
            $stmt->nextRowset(); //PERMETTE DI SVUOTARE LE QUERY IN CORSO(PULISCE LA CODA DELLE QUERY), SENZA DI QUELLE DOVEVO CHIUDERE PER FORZA LA CONNESSIONE
            //PER FARE LA DOMANDA CON AUTOMATICO, PER PRENDERE L'ID AUTOMATICO E METTERLO NELLA RISPOSTA
            $selectStmt = $conn->query("SELECT @new_id AS newid");
            $result = $selectStmt->fetch(PDO::FETCH_ASSOC);//ritorna un array indicizzato secondo il nome delle colonne 

            $newID = $result['newid'];
            $conn = null;
        }else{
            echo "<script>alert('Alcuni campi sono vuoti');</script>";
        }   
    }      
  ?>
  <?php

if(isset($_POST['inviaChiusa'])){
    $testo=$_POST["testo"];
    $punteggio=$_POST["punteggio"];
    $foto=$_POST['foto'];       
    $dominio = $_SESSION['domain'];
    if(!empty($testo) and !empty($punteggio) and !empty($_POST['opzione1']) and !empty($_POST['opzione2'])){
        $query = "CALL Inserisci_DOMANDA_CHIUSA_";
        $query .= $dominio;
        $query .= "(:testo, :punteggio, :foto, :code, :codice_sondaggio, @new_id)" ;
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':testo', $testo);
        $stmt->bindParam(':punteggio', $punteggio);
        $stmt->bindParam(':foto', $foto);
        if($dominio === "AZIENDA"){
            $code = $_SESSION['codice'];
            $stmt->bindParam(':code', $code);
        }else if($dominio === "PREMIUM"){
            $code=$_SESSION['email'];
            $stmt->bindParam(':code', $code);
        }
        $stmt->bindParam(':codice_sondaggio', $codice_sondaggio);

        $stmt->execute();
        $document = [
            "messaggio" => "Creazione Domanda Aperta",
            "chi" => "$code",
            "nome" => "$testo",
            "date" => new MongoDB\BSON\UTCDateTime()
          ];
          $bulk = new MongoDB\Driver\BulkWrite;
          $bulk->insert($document);
          $m->executeBulkWrite("$dbname.$collection", $bulk);
        // Esecuzione della stored procedure   
        $stmt->nextRowset(); 
        $selectStmt = $conn->query("SELECT @new_id AS newid");
        $result = $selectStmt->fetch(PDO::FETCH_ASSOC);
        $newID = $result['newid'];
        //INSERIMENTO OPZIONI
        //id domanda, num opzione contatore , testo opzione
        $contatore = 1;
        $testo_opzione = $_POST['opzione1'];
        $query_prima_opzione = "CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(?, ?, ?)";
        $query_seconda_opzione = "CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(?, ?, ?)";
        $query_add_opzione = "CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(?, ?, ?)";
        
        $stmt_prima = $conn->prepare($query_prima_opzione);
        $stmt_seconda = $conn->prepare($query_seconda_opzione);
        $stmt_add = $conn->prepare($query_add_opzione);

        $stmt_prima->bindParam(1, $newID, PDO::PARAM_STR);
        $stmt_prima->bindParam(2, $contatore, PDO::PARAM_STR);
        $stmt_prima->bindParam(3, $testo_opzione, PDO::PARAM_STR);
        $stmt_prima->execute();
        $document = [
            "messaggio" => "Creazione Opzione Domanda Chiusa",
            "chi" => "$code",
            "domanda_id" => "$newID",
            "nome" => "$testo_opzione",
            "date" => new MongoDB\BSON\UTCDateTime()
          ];
          $bulk = new MongoDB\Driver\BulkWrite;
          $bulk->insert($document);
          $m->executeBulkWrite("$dbname.$collection", $bulk);

        $contatore = 2;
        $testo_opzione_2 = $_POST['opzione2'];
        if($testo_opzione_2 === $testo_opzione_1 ){
            header("Location: FormCreazioneDomande.php");
        }
        $stmt_seconda->bindParam(1, $newID, PDO::PARAM_STR);
        $stmt_seconda->bindParam(2, $contatore, PDO::PARAM_STR);
        $stmt_seconda->bindParam(3, $testo_opzione_2, PDO::PARAM_STR);
        $stmt_seconda->execute();
        $document = [
            "messaggio" => "Creazione Opzione Domanda Chiusa",
            "chi" => "$code",
            "domanda_id" => "$newID",
            "nome" => "$testo_opzione_2",
            "date" => new MongoDB\BSON\UTCDateTime()
          ];
          $bulk = new MongoDB\Driver\BulkWrite;
          $bulk->insert($document);
          $m->executeBulkWrite("$dbname.$collection", $bulk);

        $contatore = 3;
        while(true){
            if(isset($_POST[$contatore])){
                $testo_opzione_add = $_POST[$contatore];
                if(!empty($testo_opzione_add)){
                $stmt_add->bindParam(1, $newID, PDO::PARAM_STR);
                $stmt_add->bindParam(2, $contatore, PDO::PARAM_STR);
                $stmt_add->bindParam(3, $testo_opzione_add, PDO::PARAM_STR);
                $stmt_add->execute();
                $document = [
                    "messaggio" => "Creazione Opzione Domanda Chiusa",
                    "chi" => "$code",
                    "domanda_id" => "$newID",
                    "nome" => "$testo_opzione_add",
                    "date" => new MongoDB\BSON\UTCDateTime()
                  ];
                  $bulk = new MongoDB\Driver\BulkWrite;
                  $bulk->insert($document);
                  $m->executeBulkWrite("$dbname.$collection", $bulk);
                }else{

                }
            }else{
                break;
            }
            $contatore = $contatore + 1;
        }
            

        header("Location: FormCreazioneDomande.php");
        $conn = null;
        }else{
            echo "<script>alert('Alcuni campi sono vuoti');</script>";
        }
    }
         
?>
</form>


</body>
</hmtl>