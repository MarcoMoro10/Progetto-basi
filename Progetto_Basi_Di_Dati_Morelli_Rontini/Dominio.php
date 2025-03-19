<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <link href="prova.css" rel="stylesheet" type="text/css">
</head>
<body>
<form action="Dominio.php" method="post">
  <h1> CREA CATEGORIA</h1>
  
  <label for="parola">Parola:</label>
  <input type="parola" name="parola" id="parola">
  
  <label for="descrizione">Descrizione:</label>
  <input type="descrizione" name="descrizione" id="descrizione">

  <input type="submit" name="Dominio" value="Dominio">
  <?php 
     session_start();
     error_reporting(0);
     require_once 'conn.php';
     require_once 'connMongo.php';
    if(isset($_POST["parola"]) && isset($_POST["descrizione"])){
      $parola=$_POST["parola"];
      $descrizione=$_POST["descrizione"];
      $mail=$_SESSION['email'];
      if(!empty($_POST["parola"]) and !empty($_POST["descrizione"])){
        $query = "CALL Inserisci_DOMINIO (?, ?, ?)" ;
        $stmt = $conn->prepare($query);
        // Impostazione dei parametri in input
        $stmt->bindParam(1, $parola, PDO::PARAM_STR);
        $stmt->bindParam(2, $descrizione, PDO::PARAM_STR);
        $stmt->bindParam(3, $mail, PDO::PARAM_STR);
        // Esecuzione della stored procedure
        $stmt->execute();
        $document = [
          "messaggio" => "Creazione Dominio",
          "chi" => "$mail",
          "nome" => "$parola",
          "date" => new MongoDB\BSON\UTCDateTime()
        ];
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->insert($document);
        $m->executeBulkWrite("$dbname.$collection", $bulk);
        echo("Inserito");
        header("Location: Sondaggi.php");
        $conn = null;
      }else{
        header("Location: Dominio.php");
      }
   }
   if(!isset($_POST['categoria'])){
    echo "<script>alert('Campi vuoti Dominio');</script>";
   }

  
  ?>
</form>
<form method="post" action="Sondaggi.php">
    <input type="submit" id="back" name="back" value="Torna a Sondaggi"> 
</form>
</body>
</html>