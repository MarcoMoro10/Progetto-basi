<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link href="standard.css" rel="stylesheet" type="text/css">

</head>
<body>
    <form action= "ControlloCodiceAzienda.php" method="post">
<?php
      session_start();
      error_reporting(0);	
      try {
        require_once 'conn.php';//connession
        if (isset($_POST["email"]) and isset($_POST["password"]) and isset($_POST["dominio"])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $_SESSION['codice']=$_POST['codice'];
            $dominio=$_POST["dominio"];
            $queryLog = "SELECT * FROM ";
      if($dominio === "AZIENDA" && !isset($_SESSION['codice'])){
            if($dominio === "AZIENDA"){
                $queryLog .= $dominio;
                $queryLog .= " WHERE Email=:email AND Password=SHA2(:password, 256)";
            }
    
                $stmt = $conn->prepare($queryLog);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $password);
                $stmt->execute();

                if ($stmt->rowCount() == 1) {
                    echo("Inserisci Codice di verifica");
                    echo'<br>';
                    $_SESSION['domain'] = $dominio;
                    echo'<input type="text" id="codice" name="codice">';
                    echo '<a href="ControlloCodiceAzienda.php">
						<button>Invia</button>
					</a>';
                } else {
                    $_SESSION['error']="Email o Password errati";
                    
                    header("Location: LoginIniziale.php");
                    echo($queryLog);
                }
            
           





        }else if($dominio==="PREMIUM" || $dominio==="UTENTE" || $dominio==="AMMINISTRATORE" ){
            //header("Location: login.php");
            
                if($dominio === "UTENTE"){
                    $queryLog .= $dominio;
                    $queryLog .= " WHERE Email=:email AND Password=SHA2(:password, 256)";
                }else{
                    $queryLog .= $dominio;
                    $queryLog .= ",UTENTE";
                    $queryLog .= " WHERE ";
                    $queryLog .= $dominio;
                    $queryLog .= ".Email=:email AND ";
                    $queryLog .= $dominio;
                    $queryLog .= ".Email=UTENTE.Email";
                    $queryLog .= " AND Password=SHA2(:password, 256)";
                }
                echo($queryLog);
                $stmt = $conn->prepare($queryLog);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $password);
                $stmt->execute();

                if ($stmt->rowCount() == 1) {
                    echo($dominio);
                    $_SESSION['domain'] = $dominio;
                    $_SESSION['email']=$email;
                    header("Location: Sondaggi.php");
                    echo($email);
                    exit();
                } else {
                    $_SESSION['error']="Email o Password errati";
                    
                    header("Location: LoginIniziale.php");
                    echo($queryLog);
                }
          
          
            }
            
        } 
                
            } catch(PDOException $e) {
                $error = "Errore di connessione al database: " . $e->getMessage();
                echo("sono nel catch");
            }

            $conn = null;
 ?>
</form>
</body>


                
                
                
                