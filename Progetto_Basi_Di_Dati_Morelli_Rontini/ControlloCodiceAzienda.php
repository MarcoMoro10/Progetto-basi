<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <link href="standard.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
    session_start();
    require_once 'conn.php';
    if (isset( $_POST['codice']) ) {
        $codice_azienda=$_POST["codice"];
        $query="SELECT * FROM AZIENDA ";
        $query.="WHERE Codice_Fiscale=:codice_azienda";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':codice_azienda', $codice_azienda);
        $stmt->execute();
        
        if($stmt->rowCount() ==1){
            $_SESSION['codice'] = $_POST["codice"];
        
            // do something with the value
            echo "You entered: " . $_SESSION['codice'];
            header("Location: Sondaggi.php");

            exit();
        }else{
            $_SESSION['error']="errore";
            header("Location: LoginIniziale.php");
        }
    }else{
    }
?>
</body>
</hmtl>