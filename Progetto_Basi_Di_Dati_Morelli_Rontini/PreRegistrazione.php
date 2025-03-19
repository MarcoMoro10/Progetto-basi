<?php
    session_start();
    $_SESSION['error_registrazione'] = "first";
    if(isset($_POST["dominio"])){
        $dominio=$_POST["dominio"];
        if($dominio === "UTENTE"){
            header("Location: InserisciCampiUtente.php");
        }else if($dominio === "AZIENDA"){
            header("Location: InserisciCampiAzienda.php");
        }else{
            header("Location: LoginIniziale.php");
        }
    }
?>