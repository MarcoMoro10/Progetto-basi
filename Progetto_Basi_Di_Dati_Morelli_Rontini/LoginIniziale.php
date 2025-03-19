<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <link href="prova.css" rel="stylesheet" type="text/css">
</head>
<body>
<form action="Validazione.php" method="post">
  <h1> BENVENUTO ALLA LOGIN! </h1>
  <?php
 // chi l'ha fatto, data, dati più rilevanti
  session_start();
    if(isset($_SESSION['error']) and $_SESSION['error'] != "no" and !isset($_POST['back'])){
      echo "<script>alert('Credenziali non corrette');</script>";
    }
  ?>
  
  <label for="email">Email:</label>
  <input type="email" name="email" id="email">
  
  <label for="password">Password:</label>
  <input type="password" name="password" id="password">
  
  <fieldset>
    <legend>Dominio</legend>
    <select name="dominio" id="dominio">
    <option value="UTENTE" selected="selected">UTENTE </option>
    <option value="PREMIUM">PREMIUM </option>
    <option value="AZIENDA">AZIENDA </option>
    <option value="AMMINISTRATORE">AMMINISTRATORE </option>
    </select>
    </fieldset>
    <input type="submit" id="<?php$dominio = $_POST['dominio']; echo($dominio); ?>" name="<?php$dominio = $_POST['dominio']; echo($dominio); ?>" value="Login" > <!--login.php   onclick="this.form.target='_blank';"-->

    <input type="submit" name="Registrazione" value="Registrazione" onclick="this.form.target=''; this.form.action='PreRegistrazione.php';">
  
  

</form>
</body>
</html>
