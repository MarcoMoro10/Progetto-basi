<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link href="prova.css" rel="stylesheet" type="text/css">

</head>
<body>
<h1>Abbonamento Premium</h1>
<h1>Tariffa 10 euro al mese</h1>
<form action="InserisciNuovoPremium.php" method="post"> 

  <label for="data_A"> Data Inizio:</label>
  <input type="date" name="data_A" id="data_A">

  <label for="data_C">Data Fine:</label>
  <input type="date" name="data_C" id="data_C">
<?php 
        require_once 'conn.php';
        require_once 'connMongo.php';
        session_start();
        error_reporting(0);
        echo'<input type="submit" id="crea_premium" name="crea_premium" value="Abbonati">';
        $current_timestamp = time();
    if(isset($_POST["data_A"]) && isset($_POST["data_C"]) && strtotime($_POST['data_A']) > $current_timestamp && strtotime($_POST['data_A']) < strtotime($_POST['data_C'])){
        $Data_C=$_POST['data_C'];
        $Data_A=$_POST['data_A'];
        $costo = 0.3;
        $_SESSION['costo'] = $costo;
        $data1 = DateTime::createFromFormat('Y-m-d', $_POST['data_A']);
        $data2 = DateTime::createFromFormat('Y-m-d', $_POST['data_C']);
        $interval = date_diff($data1, $data2);
        $costo = $costo * $interval->format('%R%a giorni');
        echo("Ti sarà addebitato: ");
        echo($costo . "$");
        echo("   Sarai premium fino al: " . $Data_C);
        $mail=$_SESSION['email'];
        $query = "CALL Inserisci_PREMIUM (?,?, ?, ?)" ;
        $stmt = $conn->prepare($query);
        $document = [
        "messaggio" => "Nuovo Abbonato Premium",
        "chi" => "$mail",
        "date" => new MongoDB\BSON\UTCDateTime()
        ];
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->insert($document);
        
        $m->executeBulkWrite("$dbname.$collection", $bulk);
        // Impostazione dei parametri in input
        $stmt->bindParam(1, $mail, PDO::PARAM_STR);
        $stmt->bindParam(2, $costo, PDO::PARAM_STR);
        $stmt->bindParam(3, $Data_A, PDO::PARAM_STR);
        $stmt->bindParam(4, $Data_C, PDO::PARAM_STR);
        // Esecuzione della stored procedure
        $stmt->execute();
        header("Location:SOndaggi.php");
        $conn = null;
    }else{
        if(isset($_POST['crea_premium'])){
            echo "<script>alert('Le date non vanno bene');</script>";
        }
    }
  ?>
</form>
    <form method="post" action = "Sondaggi.php">
    <input type="submit" name="back" value="Ritorna a sondaggi">
    </form>
</body>
</hmtl>