<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <link href="prova.css" rel="stylesheet" type="text/css">
</head>
<body>
<form method="post">
<h1> REGISTRAZIONE AZIENDA </h1>
    <?php 
        session_start();
        $_SESSION['error']="no";
        if($_SESSION['error_registrazione'] == "vuoto"){
          echo "<script>alert('Email o Password vuoti');</script>";
        }else if($_SESSION['error_registrazione'] == "registrato"){
          echo "<script>alert('Già registrata');</script>";
        }else{
          
        }
    ?>
  
  <label for="codice">Codice_Fiscale:</label>
  <input type="codice" name="codice" id="codice">
  
  <label for="email">Email:</label>
  <input type="email" name="email" id="email">
  
  <label for="password">Password:</label>
  <input type="password" name="password" id="password">
  
  <label for="nome">Nome:</label>
  <input type="nome" name="nome" id="nome">
  
  <label for="sede">Sede:</label>
  <input type="sede" name="sede" id="sede">

  <input type="submit" name="dominio" value="AZIENDA" onclick="this.form.target=''; this.form.action='Registrazione.php';">
</form>
<form method="post" action="LoginIniziale.php">
    <input type="submit" id="back" name="back" value="Indietro"> 
</form>
</body>
</html>