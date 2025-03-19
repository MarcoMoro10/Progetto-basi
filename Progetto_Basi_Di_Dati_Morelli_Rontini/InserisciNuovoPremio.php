<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <link href="prova.css" rel="stylesheet" type="text/css">
</head>
<body>
<form action="InserisciNuovoPremio.php" method="post">
    <h1> CREA PREMIO </h1>

    <label for="nome">Nome Premio:</label>
    <input type="nome" name="nome" id="nome">
    
    <label for="descrizione">Descrizione:</label>
    <input type="descrizione" name="descrizione" id="descrizione">
    
    <label for="foto">Inserisci URL foto:</label>
    <input type="foto" name="foto" id="foto">
  
    <label for="punti">Inserisci minimo punti:</label>
    <input type="punti" name="punti" id="punti">

    <input type="submit" name="eseguiquery" value="Crea">

    <?php
    require_once 'conn.php';
    require_once 'connMongo.php';
    session_start();
    error_reporting(0);
    if(isset($_POST['eseguiquery'])){
        $query = "CALL Inserisci_PREMI_DISPONIBILI(?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $codice = rand();
        $email_ammi=$_SESSION['email'];
        $nome = $_POST['nome'];
        $descrizione = $_POST['descrizione'];
        $foto = $_POST['foto'];
        $minimo_punti = $_POST['punti'];
        $stmt->bindParam(1, $codice, PDO::PARAM_STR);
        $stmt->bindParam(2, $email_ammi, PDO::PARAM_STR);
        $stmt->bindParam(3, $nome, PDO::PARAM_STR);
        $stmt->bindParam(4, $foto, PDO::PARAM_STR);
        $stmt->bindParam(5, $descrizione, PDO::PARAM_STR);
        $stmt->bindParam(6, $minimo_punti, PDO::PARAM_STR);
        if(empty($nome)){
        }else{
            $stmt->execute();
            $document = [
                "messaggio" => "Crea Premio",
                "chi" => "$email_ammi",
                "nome" => "$descrizione",
                "date" => new MongoDB\BSON\UTCDateTime()
            ];
            $bulk = new MongoDB\Driver\BulkWrite;
            $bulk->insert($document);
            $m->executeBulkWrite("$dbname.$collection", $bulk);
            header("Location: Sondaggi.php");
        }
    }
    if(!isset($_POST['inserisci_new_premio'])){
        echo "<script>alert('Campi vuoti Premio');</script>";
    }
    ?>
</form>
<form method="post" action="Sondaggi.php">
        <input type="submit" id="menu" name="menu" value="Torna al menu">
    </form>
</body>
</html>
