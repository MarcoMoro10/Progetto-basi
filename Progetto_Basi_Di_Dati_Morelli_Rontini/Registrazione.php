<?php
    session_start();
    require_once 'conn.php';
    require_once 'connMongo.php';
// Connect to database
$email=$_POST["email"];
$password=$_POST["password"];
$dominio=$_POST["dominio"];
if(!empty($email) && !empty($password)){
$nome=$_POST["nome"];


if($dominio==="UTENTE"){
  $queryLog = "SELECT * FROM UTENTE WHERE Email=:email";
  $stmt=$conn->prepare($queryLog);
  $stmt->bindParam(':email', $email);
}elseif($dominio==="AZIENDA"){
  $codice_fiscale=$_POST["codice"];
  $queryLog = "SELECT * FROM AZIENDA WHERE Codice_Fiscale=:codice";
  $stmt=$conn->prepare($queryLog);
  $stmt->bindParam(':codice', $codice_fiscale);
}


$stmt->execute();


if ($stmt->rowCount() == 1) {
  $_SESSION['error_registrazione'] = "registrato";
    if($dominio==="AZIENDA"){
      header("Location:InserisciCampiAzienda.php");
    }else{
      header("Location:InserisciCampiUtente.php");
    }
    //se esiste
} else {
    //se non esiste
    $query = "CALL Inserisci_" ;
    $query .= $dominio;
  
    if($dominio === "AZIENDA"){
      $query .= "(?, ?, ?, ?, ?)";
      $stmt = $conn->prepare($query);
      // Impostazione dei parametri in input
      $sede=$_POST["sede"];
      
      if(!empty($codice_fiscale) && !empty($nome) && !empty($sede)){
        $stmt->bindParam(1, $codice_fiscale, PDO::PARAM_STR);
        $stmt->bindParam(2, $email, PDO::PARAM_STR);
        $stmt->bindParam(3, $password, PDO::PARAM_STR);
        $stmt->bindParam(4, $nome, PDO::PARAM_STR);
        $stmt->bindParam(5, $sede, PDO::PARAM_STR);
        // Esecuzione della stored procedure
        $stmt->execute();
        $document = [
          "messaggio" => "Registrazione Azienda",
          "chi" => "$codice_fiscale",
          "date" => new MongoDB\BSON\UTCDateTime()
        ];
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->insert($document);
        
        $m->executeBulkWrite("$dbname.$collection", $bulk);
        header("Location: LoginIniziale.php");
      $conn = null;
      }else{
        header("Location:InserisciCampiAzienda.php");
      }
    }else if($dominio === "UTENTE"){
      $query .= "(?, ?, ?, ?, ?, ?)";
      $stmt = $conn->prepare($query);
      // Impostazione dei parametri in input
      $cognome=$_POST["cognome"];
      $luogo=$_POST["luogo"];
      $anno=$_POST["date"];
      
      if( !empty($nome) && !empty($cognome) && !empty($luogo) && !empty($anno)){

      
      $stmt->bindParam(1, $email, PDO::PARAM_STR);
      $stmt->bindParam(2, $password, PDO::PARAM_STR);
      $stmt->bindParam(3, $nome, PDO::PARAM_STR);
      $stmt->bindParam(4, $cognome, PDO::PARAM_STR);
      $stmt->bindParam(5, $luogo, PDO::PARAM_STR);
      $stmt->bindParam(6, $anno, PDO::PARAM_STR);
       // Esecuzione della stored procedure
      $stmt->execute();
      $document = [
        "messaggio" => "Registrazione Utente",
        "chi" => "$email",
        "date" => new MongoDB\BSON\UTCDateTime()
      ];
      $bulk = new MongoDB\Driver\BulkWrite;
      $bulk->insert($document);
      
      $m->executeBulkWrite("$dbname.$collection", $bulk);
      header("Location: LoginIniziale.php");
      $conn = null;
    }else{
      header("Location:InserisciCampiUtente.php");
    }
  }
  }
}else{
  if($_SESSION['error_registrazione'] == "first"){
    $_SESSION['error_registrazione'] = "vuoto";
  }
  

  if($dominio==="AZIENDA"){
    header("Location:InserisciCampiAzienda.php");
  }else{
    header("Location:InserisciCampiUtente.php");
  }
}
?>