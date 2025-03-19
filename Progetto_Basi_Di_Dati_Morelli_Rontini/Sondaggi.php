<!DOCTYPE html>
<html>
<head>
<link href="Sondaggi.css" rel="stylesheet" type="text/css">
</head>
<body>
	<?php	// inclusione del file HTML
	//per evitare output di warning
			
	require_once 'conn.php';
	require_once 'connMongo.php';
	error_reporting(0);	
	session_start();
		?>
	<h1>Tabella Informazioni EFORM</h1>
	<form method="post" action="LoginIniziale.php">
    <input type="submit" id="back" name="back" value="Logout"> 
	<style>
		 #back{
				position: right;
				display: flex;
				align-items: end;
				float: none;
				margin-left: auto;
				margin-right: 0;
				width:100px;
			  }
	</style>
</form>
	
	<form action="Sondaggi.php" method="post">
	<?php
	if($_SESSION['domain'] != "AZIENDA"){
	?>

	<?php
	$categoria= filter_input(INPUT_POST,"categoria", FILTER_SANITIZE_STRING);
		$query="SELECT Parola FROM DOMINIO";
		$stmt = $conn->prepare($query);
		$stmt->execute();		
			echo('  <select name="categoria" id="categoria"> ');
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				
				echo "<option value='".$row["Parola"]."'>".$row["Parola"]." </option>";
			}
		echo('</select>');
	?>  
	<br>
	<fieldset> 
  	<input type="submit" name="Sondaggi" value="Visualizza Sondaggi" >
	<input type="submit" id="visualizza_domini" name="visualizza_domini" value='Visualizza I Domini Interessati'>
    <input type="submit" id="visualizza" name="visualizza" value='Visualizza I Tuoi Sondaggi Invitati'>
	<input type="submit" id="premi" name="premi" value='Visualizza I Tuoi Premi'>
	<?php
		echo '<input type="submit" id="inviti" name="inviti" value="Inviti a sondaggi">';
		echo '<input type="submit" id="premi_disponibili" name="premi_disponibili" value="Visualizza Premi Disponibili">';
		echo '<input type="submit" id="classifica" name="classifica" value="Visualizza Classifica">';
		}
		if($_SESSION['domain'] === "UTENTE"){
			echo '<input type="submit" id="diventa_premium" name="diventa_premium" value="Diventa Premium">';	
		}
	?>
	</fieldset>
</form>
<form mehot="post" action ="FormCreazioneSondaggio.php">
	<?php 
			//$_SESSION['dominio']=$_POST['dominio'];
			if($_SESSION['domain'] === "PREMIUM" || $_SESSION['domain']==="AZIENDA"){
				?>
				<input type="submit" id="crea_sondaggio" name="crea_sondaggio" value="Crea Sondaggio"> 
				</form>

				<form method="post" action="AssegnazioneDomande.php">
				<input type="submit" id="aggiungi_domanda" name="aggiungi_domanda" value="Aggiungi Domanda"> 
				</form>
				
				<form method="post" action="Sondaggi.php">
				<?php
				echo'<input type="submit" id="views" name="views" value="Visualizza le risposte ai tuoi sondaggi">'; 
				echo'<br>';
				echo'<input type="submit" id="views_delle_domande" name="views_delle_domande" value="Visualizza le domande che hai creato">'; 
			}
			if(isset($_SESSION['email'])){	
				$mail = $_SESSION['email'];
			}
			
	?>
	
</form>
	
	<form method = "post" action = "Domande.php">
	<?php
	if(isset($_POST['premi'])){
		//nome descrizione foto
			$query_visualizza_premio = "SELECT * FROM PREMI_DISPONIBILI WHERE Codice IN (SELECT Codice_Premio FROM VINCITA WHERE Email_Utente = '$mail')";
			$stmt_visualizza_premio = $conn->query($query_visualizza_premio);
			while($row_visualizza_premio = $stmt_visualizza_premio->fetch(PDO::FETCH_ASSOC)) {
			$domanda_visualizza_premio[] = $row_visualizza_premio;
			}
			echo '<table>';
			echo '<thead>';
			echo	'<tr>';
			echo		'<th>Nome Premio</th>';
			echo		'<th>Descrizione</th>';
			echo		'<th>Foto</th>';
			echo		'<th></th>';
			echo	'</tr>';
			echo '</thead>';
			echo'<tbody>';
			if(isset($domanda_visualizza_premio)){
				foreach($domanda_visualizza_premio as $codice_sondaggi_premio) :
					echo'<tr>';
					$input_id =1; 
					echo "<td for= '".$input_id."'>" .$codice_sondaggi_premio['Nome'].'</td>';
					echo "<td for= '".$input_id."'>" .$codice_sondaggi_premio['Descrizione'].'</td>';
					?><td for="<?php echo $input_id; ?>"><img src="<?php echo($codice_sondaggi_premio['Foto']);?>"/></td><?php
					echo'</tr>';
				endforeach;
			}
	}
	?>
	<style>
			img {
					width: 300px;
					height: 200px;
				}
					  </style>
	</form>
	<form method="post" action="Sondaggi.php">
	<?php
	if(isset($_POST['visualizza'])){
		$query_visualizza = "SELECT * FROM SONDAGGIO WHERE SONDAGGIO.Codice IN (
			SELECT POSSESSO.Codice_Sondaggio
			FROM DOMANDA,RISPOSTA,POSSESSO
			WHERE RISPOSTA.Email_Utente = '$mail' AND RISPOSTA.Id_Domanda = DOMANDA.Id AND POSSESSO.Id_Domanda=DOMANDA.Id)";
		$stmt_visualizza = $conn->query($query_visualizza);
		while($row_visualizza = $stmt_visualizza->fetch(PDO::FETCH_ASSOC)) {
			$domanda_visualizza[] = $row_visualizza;
		}
		echo '<table>';
			echo '<thead>';
			echo	'<tr>';
			echo		'<th>Codice</th>';
			echo		'<th>Titolo</th>';
			echo		'<th>Data_Apertura</th>';
			echo		'<th>Data_Chiusura</th>';
			echo		'<th>Max_Utenti</th>';
			echo		'<th>Stato</th>';
			echo		'<th></th>';
			echo	'</tr>';
			echo '</thead>';
		
			echo'<tbody>';
		if(isset($domanda_visualizza)){
			foreach($domanda_visualizza as $codice_sondaggi) :
				echo'<tr>';
				$input_id =1; 
				echo "<td for= '".$input_id."'>" .$codice_sondaggi['Codice'].'</td>';
				echo "<td for= '".$input_id."'>" .$codice_sondaggi['Titolo'].'</td>';
				echo "<td for= '".$input_id."'>" .$codice_sondaggi['Data_Apertura'].'</td>';
				echo "<td for= '".$input_id."'>" .$codice_sondaggi['Data_Chiusura'].'</td>';
				echo "<td for= '".$input_id."'>" .$codice_sondaggi['Max_Utenti'].'</td>';
				echo "<td for= '".$input_id."'>" .$codice_sondaggi['Stato'].'</td>';
				echo"<td>" ;
				echo'<input type="submit" id="Codice" name="Codice" value="'.$codice_sondaggi['Codice'].'">'; //<?php echo($codice_sondaggi['Codice']); 
				echo"</td>"; 
				echo'</tr>';
			endforeach;
		}
	}
	if(isset($_POST["Codice"])){
		$codice=$_POST['Codice'];
		$mail=$_SESSION['email'];
		$query_visualizza_risposte = "SELECT * FROM RISPOSTA,DOMANDA,POSSESSO,SONDAGGIO WHERE Email_Utente='$mail' AND DOMANDA.Id=RISPOSTA.Id_Domanda AND SONDAGGIO.Codice = '$codice' AND SONDAGGIO.Codice=POSSESSO.Codice_Sondaggio AND POSSESSO.Id_domanda = Domanda.Id";
		$stmt_visualizza_risposte = $conn->query($query_visualizza_risposte);
		while($row_visualizza_risposte = $stmt_visualizza_risposte->fetch(PDO::FETCH_ASSOC)) {
			$domanda_visualizza_risposte[] = $row_visualizza_risposte;
		}
		echo '<table>';
		echo '<thead>';
		echo	'<tr>';
		echo		'<th>Testo Domanda</th>';
		echo		'<th>Risposta</th>';
		echo		'<th></th>';
		echo	'</tr>';
		echo '</thead>';
		echo'<tbody>';
		foreach($domanda_visualizza_risposte as $codice_sondaggi_risposte) :
			echo'<tr>';
			$input_id =1; 
			echo "<td for= '".$input_id."'>" .$codice_sondaggi_risposte['Testo'].'</td>';
			echo "<td for= '".$input_id."'>" .$codice_sondaggi_risposte['Testo_Risposta'].'</td>';
			echo'</tr>';
		endforeach;
	}
	if(isset($_POST['inviti']) || $_SESSION['check'] === "true"){
		$_SESSION['check'] = "false";
		$mail=$_SESSION['email'];
		$query_visualizza_inviti = "SELECT * FROM INVITO WHERE Email_Utente='$mail' AND Esito='Attesa' ";
		$stmt_visualizza_inviti = $conn->query($query_visualizza_inviti);
		while($row_visualizza = $stmt_visualizza_inviti->fetch(PDO::FETCH_ASSOC)) {
			$invito_visualizza[] = $row_visualizza;
		}
		echo '<table>';
			echo '<thead>';
			echo	'<tr>';
			echo		'<th>Codice Invito</th>';
			echo		'<th>Codice Sondaggio</th>';
			echo		'<th>Mittente</th>';
			echo		'<th></th>';
			echo		'<th></th>';
			echo	'</tr>';
			echo '</thead>';
		
			echo'<tbody>';
		if(isset($invito_visualizza)){
			foreach($invito_visualizza as $codice_sondaggi) :
				echo'<tr>';
				$input_id =1; 
				echo "<td for= '".$input_id."'>" .$codice_sondaggi['Codice'].'</td>';
				echo "<td for= '".$input_id."'>" .$codice_sondaggi['Codice_Sondaggio'].'</td>';
				echo "<td for= '".$input_id."'>" .$codice_sondaggi['Email_Utente_Premium'].'</td>';
				echo "<td for= '".$input_id."'>" .$codice_sondaggi['Esito'].'</td>';
				echo"<td>" ;
				echo "</form>";
				echo "<form  action ='Sondaggi.php' method='post'>";
				echo '<input type="hidden" name="id" value="'.$codice_sondaggi['Codice'].'">';
				echo "<input type='submit' name='accept' value='Accept'>";
				echo "<input type='submit' name='reject' value='Reject'>";
				echo"</td>"; 
				echo'</tr>';
				
			endforeach;
		}

	}	
	?>	
	</form>
	<?php
		if (isset($_POST['accept'])) {
			$val=$_POST["id"];
			echo($val);
			$sql = "UPDATE INVITO SET Esito = 'accepted' WHERE Codice = '$val'";
			echo($sql);
			$stmt = $conn->query($sql);
			$_SESSION['check'] = "true";
			echo($_SESSION['check']);
			header("Refresh: 0");

			
		}
		if (isset($_POST['reject'])) {
			$val=$_POST["id"];
			$sql = "UPDATE INVITO SET Esito = 'rejected' WHERE Codice = '$val'";
			$stmt = $conn->query($sql);
			$_SESSION['check'] = "true";
			header("Refresh: 0");			
		}
				
?>
<form method = "post" action="Dominio.php">
<?php
		if($_SESSION['domain']=== "AMMINISTRATORE"){
			echo'<input type="submit" id="categoria" name="categoria" value="Crea Categoria">';
		?>
</form>
<form method="post" action="InserisciPREMIO.php">
	<?php
			echo'<input type="submit" id="premio" name="premio" value="Assegna Premio">'; 
		?>
	</form>
	<form method="post" action="InserisciNuovoPremio.php">
	<?php
			echo'<input type="submit" id="inserisci_new_premio" name="inserisci_new_premio" value="Crea Premio">'; 
		
		?>
	</form>
	<form method="post" action="Sondaggi.php">
	<?php
			echo'<input type="submit" id="inserisci_new_amministratore" name="inserisci_new_amministratore" value="Aggiungi Amministratore">'; 
			echo'<br>';
			echo'<input type="submit" id="visualizza_i_tuoi_domini" name="visualizza_i_tuoi_domini" value="Mostra le tue categorie">'; 

		}
		?>
	</form>
	<form method="post" action = "Sondaggi.php">
	<?php 
		if(isset($_POST['views'])){
		//VISUALIZZA
		if($_SESSION['domain'] === "PREMIUM"){
			$query_visualizza_sondaggi = "SELECT * FROM SONDAGGIO,CREAZIONE_2 WHERE SONDAGGIO.Codice = CREAZIONE_2.Codice_Sondaggio AND CREAZIONE_2.Email_Premium='$mail'";
		}else if($_SESSION['domain'] === "AZIENDA"){
			$codice_azienda = $_SESSION['codice'];
			$query_visualizza_sondaggi = "SELECT * FROM SONDAGGIO,CREAZIONE_1 WHERE SONDAGGIO.Codice = CREAZIONE_1.Codice_Sondaggio AND CREAZIONE_1.Codice_Fiscale_Azienda='$codice_azienda'";
		}
		$stmt_visualizza_sondaggi = $conn->query($query_visualizza_sondaggi);
		while($row_visualizza_sondaggi = $stmt_visualizza_sondaggi->fetch(PDO::FETCH_ASSOC)) {
			$domanda_visualizza_sondaggi[] = $row_visualizza_sondaggi;
		}
		echo '<table>';
			echo '<thead>';
			echo	'<tr>';
			echo		'<th>Codice</th>';
			echo		'<th>Titolo</th>';
			echo		'<th>Data_Apertura</th>';
			echo		'<th>Data_Chiusura</th>';
			echo		'<th>Max_Utenti</th>';
			echo		'<th>Stato</th>';
			echo		'<th></th>';
			echo	'</tr>';
			echo '</thead>';
		
			echo'<tbody>';
		foreach($domanda_visualizza_sondaggi as $codice_view) :
			echo'<tr>';
			$input_id =1; 
			echo "<td for= '".$input_id."'>" .$codice_view['Codice'].'</td>';
			echo "<td for= '".$input_id."'>" .$codice_view['Titolo'].'</td>';
			echo "<td for= '".$input_id."'>" .$codice_view['Data_Apertura'].'</td>';
			echo "<td for= '".$input_id."'>" .$codice_view['Data_Chiusura'].'</td>';
			echo "<td for= '".$input_id."'>" .$codice_view['Max_Utenti'].'</td>';
			echo "<td for= '".$input_id."'>" .$codice_view['Stato'].'</td>';
			echo"<td>" ;
			echo'<input type="submit" id="visualizza_domande_risposte_sondaggio" name="visualizza_domande_risposte_sondaggio" value="'.$codice_view['Codice'].'">'; //<?php echo($codice_sondaggi['Codice']); 
			echo"</td>"; 
			echo'</tr>';
		endforeach;
		}


		if(isset($_POST['visualizza_domande_risposte_sondaggio'])){
			$codice_sondaggio_view = $_POST['visualizza_domande_risposte_sondaggio'];
			$query_view = "SELECT * FROM RISPOSTA,DOMANDA WHERE RISPOSTA.Id_Domanda = DOMANDA.Id AND Domanda.Id IN (SELECT Id_Domanda FROM POSSESSO WHERE Codice_Sondaggio IN (SELECT Codice FROM SONDAGGIO WHERE Codice = '$codice_sondaggio_view'))";
			$stmt_view = $conn->query($query_view);
		while($row_view = $stmt_view->fetch(PDO::FETCH_ASSOC)) {
			$domanda_view_request[] = $row_view;
		}
		echo '<table>';
			echo '<thead>';
			echo	'<tr>';
			echo		'<th>Testo</th>';
			echo		'<th>Testo Risposta</th>';
			if($_SESSION['domain'] === "PREMIUM"){
				echo		'<th>Partecipante</th>';
			}
			
			echo	'</tr>';
			echo '</thead>';
		
			echo'<tbody>';
		foreach($domanda_view_request as $codice_view_request) :
			echo'<tr>';
			$input_id =1; 
			echo "<td for= '".$input_id."'>" .$codice_view_request['Testo'].'</td>'; 
			echo "<td for= '".$input_id."'>" .$codice_view_request['Testo_Risposta'].'</td>';
			if($_SESSION['domain'] === "PREMIUM"){
				echo "<td for= '".$input_id."'>" .$codice_view_request['Email_Utente'].'</td>';
			}
			echo'</tr>';
		endforeach;
		
		}
		?>
	</form>

	<form action= "InserisciInvito.php" method="post">
	<?php
		if($_SESSION['domain']==="PREMIUM"){
			echo'<input type="submit" id="invito" name="invito" value="Invita Utenti">'; 

		}if($_SESSION['domain']==="AZIENDA"){
			echo'<input type="submit" id="invito" name="invito" value="Invita Utenti">'; 

		}
	?>
	</form>
	<form action= "MostraStats.php" method="post">
	<?php
	if($_SESSION['domain']==="PREMIUM"){
		echo'<input type="submit" id="stats" name="stats" value="Mostra Statistiche">';
		}if($_SESSION['domain']==="AZIENDA"){
			echo'<input type="submit" id="stats" name="stats" value="Mostra Statistiche">';
			}
			?>
	</form>
	<?php
		$mail = $_SESSION['email'];
		if(isset($_POST['Gestisci_Preferenza'])){
			$dominio_parola = $_POST['Gestisci_Preferenza'];
			$query_prefe = "SELECT * FROM INTERESSAMENTO WHERE Email_Utente='$mail' AND Dominio_Parola = '$dominio_parola'";
			$stmt_prefe = $conn->query($query_prefe);
            if ($stmt_prefe->rowCount() == 1) {
				//eliminazione preferenza
				echo("La tua preferenza è stata eliminata");
				$query_call = "CALL Elimina_INTERESSAMENTO(?,?)";
			}else{
				//inserimento preferenza
				echo("La tua preferenza è stata aggiunta");
				$query_call = "CALL Inserisci_INTERESSAMENTO(?,?)";
			}
			$stmt = $conn->prepare($query_call);
            $stmt->bindParam(1, $mail, PDO::PARAM_STR);
            $stmt->bindParam(2, $dominio_parola, PDO::PARAM_STR);
			$stmt->execute();
			$document = [
                "messaggio" => "Aggiunta/Rimozione Interessamento a dominio",
                "chi" => "$mail",
                "dominio" => "$dominio_parola",
                "date" => new MongoDB\BSON\UTCDateTime()
              ];
              $bulk = new MongoDB\Driver\BulkWrite;
              $bulk->insert($document);
              $m->executeBulkWrite("$dbname.$collection", $bulk);
		}
?>
<form method="post" action = "Sondaggi.php">
<?php
		if(isset($_POST['visualizza_domini'])){
			$query_visualizza_domini = "SELECT * FROM INTERESSAMENTO WHERE Email_Utente ='$mail'";
			$stmt_visualizza_domini = $conn->query($query_visualizza_domini);
			while($row_visualizza_domini = $stmt_visualizza_domini->fetch(PDO::FETCH_ASSOC)) {
				$domanda_visualizza_domini[] = $row_visualizza_domini;
			}
			echo '<table>';
				echo '<thead>';
				echo	'<tr>';
				echo		'<th>Titolo</th>';
				echo		'<th></th>';
				echo	'</tr>';
				echo '</thead>';
				echo'<tbody>';
			if(isset($domanda_visualizza_domini)){
				foreach($domanda_visualizza_domini as $codice_sondaggi_domini) :
					echo'<tr>';
					$input_id =1; 
					echo "<td for= '".$input_id."'>" .$codice_sondaggi_domini['Dominio_Parola'].'</td>';
					echo"<td>" ;
					echo'<input type="submit" id="Gestisci_Preferenza" name="Gestisci_Preferenza" value="'.$codice_sondaggi_domini['Dominio_Parola'].'">'; //<?php echo($codice_sondaggi['Codice']); 
					echo"</td>"; 
					echo'</tr>';
				endforeach;
			}
		}

		if(isset($_POST['premi_disponibili'])){
			$query_prize= "SELECT Codice,Nome,Foto,Descrizione, Minimo_Punti FROM PREMI_DISPONIBILI";
			$stmt = $conn->query($query_prize);
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$prize[] = $row;
			}
			echo'<table>';
			echo '<thead>';
			echo	'<tr>';
			echo		'<th>Codice</th>';
			echo        '<th>Nome</th>';
			echo		'<th>Foto</th>';
			echo		'<th>Descrizione</th>';
			echo		'<th>Minimo Punti</th>';
			echo	'</tr>';
			echo '</thead>';
			echo'<tbody>';
			foreach($prize as $premi) :
				echo'<tr>';
				$input_id =1; 
				echo "<td for= '".$input_id."'>" .$premi['Codice'].'</td>';
				echo "<td for= '".$input_id."'>" .$premi['Nome'].'</td>';
				echo "<td for= '".$input_id."'>" .$premi['Descrizione'].'</td>'; 
				echo '<td><img src="'.$premi['Foto'].'"></td>';   
				echo "<td for= '".$input_id."'>" .$premi['Minimo_Punti'].'</td>';  
				echo'</tr>';
			endforeach;
		}

		if(isset($_POST['classifica'])){
			$query_classifica= "SELECT  Email, Totale_Bonus FROM UTENTE order by Totale_Bonus DESC;";
			$stmt = $conn->query($query_classifica);
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$cla[] = $row;
			}
			echo'<table>';
			echo '<thead>';
			echo	'<tr>';
			echo		'<th>Email</th>';
			echo        '<th>Totale Bonus</th>';
			echo	'</tr>';
			echo '</thead>';
			echo'<tbody>';
			foreach($cla as $classy) :
				echo'<tr>';
				$input_id =1; 
				echo "<td for= '".$input_id."'>" .$classy['Email'].'</td>';
				echo "<td for= '".$input_id."'>" .$classy['Totale_Bonus'].'</td>';
				echo'</tr>';
			endforeach;
		}
		if(isset($_POST['inserisci_new_amministratore'])){
			$query_visualizza_candidato = "SELECT * FROM UTENTE WHERE Email NOT IN (SELECT Email FROM PREMIUM) AND Email NOT IN (SELECT Email FROM AMMINISTRATORE)";
			$stmt_visualizza_candidato = $conn->query($query_visualizza_candidato);
			while($row_visualizza_candidato = $stmt_visualizza_candidato->fetch(PDO::FETCH_ASSOC)) {
				$domanda_visualizza_candidato[] = $row_visualizza_candidato;
			}
			echo '<table>';
				echo '<thead>';
				echo	'<tr>';
				echo		'<th>Aggiungi Amministratore</th>';
				echo		'<th></th>';
				echo	'</tr>';
				echo '</thead>';
				echo'<tbody>';
			if(isset($domanda_visualizza_candidato)){
				foreach($domanda_visualizza_candidato as $codice_sondaggi_candidato) :
					echo'<tr>';
					$input_id =1; 
					echo "<td for= '".$input_id."'>" .$codice_sondaggi_candidato['Email'].'</td>'; 
					echo"<td>" ;
					echo'<input type="submit" id="Aggiungi_Amministratore" name="Aggiungi_Amministratore" value="'.$codice_sondaggi_candidato['Email'].'">'; 
					echo"</td>"; 
					echo'</tr>'; 
				endforeach;
				
			}
		}
		if(isset($_POST['Aggiungi_Amministratore'])){
			$query = "CALL Inserisci_AMMINISTRATORE(?)";
			$stmt = $conn->prepare($query);
			$email=$_POST['Aggiungi_Amministratore'];
			$stmt->bindParam(1, $email, PDO::PARAM_STR);
			$stmt->execute();
			$document = [
				"messaggio" => "Aggiunto Nuovo Amministratore",
				"chi" => "$email",
				"date" => new MongoDB\BSON\UTCDateTime()
			];
			$bulk = new MongoDB\Driver\BulkWrite;
			$bulk->insert($document);
			$m->executeBulkWrite("$dbname.$collection", $bulk);
		}

		if(isset($_POST['visualizza_i_tuoi_domini'])){
			$query_visualizza_domini = "SELECT * FROM DOMINIO WHERE Email_Utente_Amm=:email_amm";
			$stmt_visualizza_domini = $conn->prepare($query_visualizza_domini);
			$stmt_visualizza_domini->bindParam(":email_amm",$_SESSION['email']);
			$stmt_visualizza_domini->execute();
			while($row_visualizza_domini = $stmt_visualizza_domini->fetch(PDO::FETCH_ASSOC)) {
				$visualizza_domini[] = $row_visualizza_domini;
			}
			echo '<table>';
				echo '<thead>';
				echo	'<tr>';
				echo		'<th>Parola</th>';
				echo		'<th>Descrizione</th>';
				echo		'<th>Email</th>';
				echo	'</tr>';
				echo '</thead>';
				echo'<tbody>';
			
				foreach($visualizza_domini as $visua_domini) :
					echo'<tr>';
					$input_id =1; 
					echo "<td for= '".$input_id."'>" .$visua_domini['Parola'].'</td>'; 
					echo "<td for= '".$input_id."'>" .$visua_domini['Descrizione'].'</td>'; 
					echo "<td for= '".$input_id."'>" .$visua_domini['Email_Utente_Amm'].'</td>'; 
					echo'</tr>'; 
				endforeach;	
			
		}


		if(isset($_POST['diventa_premium'])){
			$query_premium = "SELECT * FROM PREMIUM,AMMINISTRATORE WHERE PREMIUM.Email = '$mail' Or AMMINISTRATORE.Email = '$mail'";
			$stmt_premium = $conn->query($query_premium);
            if ($stmt_premium->rowCount() >= 1) {
				echo("Sei già un utente premium o amministratore");
			}else{
				header('Location: InserisciNuovoPremium.php');
			}
		}
		if(isset($_POST['crea_premium'])){
			require_once 'conn.php';
			require_once 'connMongo.php';
			session_start();
			error_reporting(0);
			$current_timestamp = time();
		if(isset($_POST["data_A"]) && isset($_POST["data_C"]) && strtotime($_POST['data_A']) >= $current_timestamp && strtotime($_POST['data_A']) < strtotime($_POST['data_C'])){
			$Data_C=$_POST['data_C'];
			$Data_A=$_POST['data_A'];
			$costo = 0.3;
			$_SESSION['costo'] = $costo;
			$data1 = DateTime::createFromFormat('Y-m-d', $_POST['data_A']);
			$data2 = DateTime::createFromFormat('Y-m-d', $_POST['data_C']);
			$interval = date_diff($data1, $data2);
			$costo = $costo * $interval->format('%R%a giorni');
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
			$conn = null;
			echo("Sei diventato premium");
		}else{
			if(isset($_POST['crea_premium'])){
				echo "<script>alert('Le date non vanno bene');</script>";
			}
		}
		}
	?>
	</form>
	<form action="Domande.php" method="post">
	<?php

		if ( isset($_POST['Sondaggi'])=== true) {
			echo '<table>';
			echo '<thead>';
			echo	'<tr>';
			echo		'<th>Codice</th>';
			echo		'<th>Titolo</th>';
			echo		'<th>Data_Apertura</th>';
			echo		'<th>Data_Chiusura</th>';
			echo		'<th>Max_Utenti</th>';
			echo		'<th>Stato</th>';
			echo		'<th></th>';
			echo	'</tr>';
			echo '</thead>';
		
			echo'<tbody>';
				// query per recuperare i dati dal database
				$query = "SELECT Codice,Stato,Titolo,Data_Apertura,Data_Chiusura,Max_Utenti FROM SONDAGGIO ";
					if(!is_null($categoria)){
							$query.="WHERE Dominio_Parola=:dominio_parola";
							$stmt = $conn->prepare($query);
							$stmt->bindParam(':dominio_parola', $categoria);
							$stmt->execute();
						
					}else{
						$stmt=$conn->prepare($query);
						$stmt->execute();
					}
				// Cicla sui dati e crea una riga della tabella per ogni elemento
				$utenti = array();
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$utenti[] = $row;
				}
				//query per controllare se sono stato invitato ed ho accettato
				$query_visualizza = "SELECT Codice_Sondaggio FROM INVITO WHERE Esito = 'accepted' AND Email_Utente = '$mail' AND Codice_Sondaggio NOT IN(SELECT Codice FROM SONDAGGIO WHERE Stato='CHIUSO')";
				$stmt_visualizza = $conn->query($query_visualizza);
				while($row_check = $stmt_visualizza->fetch(PDO::FETCH_ASSOC)) {
					$utenti_check[] = $row_check;
				}		
				 foreach($utenti as $index=>$codice) {
				echo'<tr>';
				 $input_id =($index + 1); 
				echo "<td for= '".$input_id."'>" .$codice['Codice'].'</td>';
				echo "<td for= '".$input_id."'>" .$codice['Titolo'].'</td>';
				echo "<td for= '".$input_id."'>" .$codice['Data_Apertura'].'</td>';
				echo "<td for= '".$input_id."'>" .$codice['Data_Chiusura'].'</td>';
				echo "<td for= '".$input_id."'>" .$codice['Max_Utenti'].'</td>';
				echo "<td for= '".$input_id."'>" .$codice['Stato'].'</td>';
				echo"<td>" ;
				// controllo se sono stato invitato ed ho accettato if()
				foreach($utenti_check as $codice_check) {
					if($codice_check['Codice_Sondaggio'] === $codice['Codice']){
						echo'<input type="submit" id="Codice" name="Codice" value="'.$codice['Codice'].'">'; 
						break;
					}else{	
					}
				}
				//echo[$utenti_check[0]];
				echo"</td>"; 
				echo'</tr>'; 
				}
			echo'</tbody>';
		echo'</table>';
		?>
		</form>
		<form method = "post" action = "Sondaggi.php">
		<?php
		echo'<input type="submit" id="Gestisci_Preferenza" name="Gestisci_Preferenza" value="'.$_POST['categoria'].'">';
	}
	?> 
	</form>
	<form method="post" action = "Sondaggi.php">
	<?php 
		if(isset($_POST['views_delle_domande'])){
		//VISUALIZZA
		if($_SESSION['domain'] === "PREMIUM"){
			$query_visualizza_sondaggi = "SELECT * FROM SONDAGGIO,CREAZIONE_2 WHERE SONDAGGIO.Codice = CREAZIONE_2.Codice_Sondaggio AND CREAZIONE_2.Email_Premium='$mail'";
		}else if($_SESSION['domain'] === "AZIENDA"){
			$codice_azienda = $_SESSION['codice'];
			$query_visualizza_sondaggi = "SELECT * FROM SONDAGGIO,CREAZIONE_1 WHERE SONDAGGIO.Codice = CREAZIONE_1.Codice_Sondaggio AND CREAZIONE_1.Codice_Fiscale_Azienda='$codice_azienda'";
		}
		$stmt_visualizza_sondaggi = $conn->query($query_visualizza_sondaggi);
		while($row_visualizza_sondaggi = $stmt_visualizza_sondaggi->fetch(PDO::FETCH_ASSOC)) {
			$domanda_visualizza_sondaggi[] = $row_visualizza_sondaggi;
		}
		echo '<table>';
			echo '<thead>';
			echo	'<tr>';
			echo		'<th>Codice</th>';
			echo		'<th>Titolo</th>';
			echo		'<th>Data_Apertura</th>';
			echo		'<th>Data_Chiusura</th>';
			echo		'<th>Max_Utenti</th>';
			echo		'<th>Stato</th>';
			echo		'<th></th>';
			echo	'</tr>';
			echo '</thead>';
		
			echo'<tbody>';
		foreach($domanda_visualizza_sondaggi as $codice_view) :
			echo'<tr>';
			$input_id =1; 
			echo "<td for= '".$input_id."'>" .$codice_view['Codice'].'</td>';
			echo "<td for= '".$input_id."'>" .$codice_view['Titolo'].'</td>';
			echo "<td for= '".$input_id."'>" .$codice_view['Data_Apertura'].'</td>';
			echo "<td for= '".$input_id."'>" .$codice_view['Data_Chiusura'].'</td>';
			echo "<td for= '".$input_id."'>" .$codice_view['Max_Utenti'].'</td>';
			echo "<td for= '".$input_id."'>" .$codice_view['Stato'].'</td>';
			echo"<td>" ;
			echo'<input type="submit" id="visualizza_domande_sondaggio" name="visualizza_domande_sondaggio" value="'.$codice_view['Codice'].'">'; //<?php echo($codice_sondaggi['Codice']); 
			echo"</td>"; 
			echo'</tr>';
		endforeach;
		}


		if(isset($_POST['visualizza_domande_sondaggio'])){
			
			$codice_sondaggio_view = $_POST['visualizza_domande_sondaggio'];
			
			$query_view = "SELECT * FROM DOMANDA WHERE Domanda.Id IN (SELECT Id_Domanda FROM POSSESSO WHERE Codice_Sondaggio IN (SELECT Codice FROM SONDAGGIO WHERE Codice = :codice_sonda))";
			$stmt_view = $conn->prepare($query_view);
			$stmt_view-> bindParam(":codice_sonda",$codice_sondaggio_view);
			$stmt_view->execute();
		while($row_view = $stmt_view->fetch(PDO::FETCH_ASSOC)) {
			$domanda_view_request[] = $row_view;
		}
		
		echo '<table>';
			echo '<thead>';
			echo	'<tr>';
			echo		'<th>Testo</th>';
			echo	'</tr>';
			echo '</thead>';
		
			echo'<tbody>';
		foreach($domanda_view_request as $codice_view_request) :
			echo'<tr>';
			$input_id =1; 
			echo "<td for= '".$input_id."'>" .$codice_view_request['Testo'].'</td>'; 
			echo'</tr>';
		endforeach;
		
		}
		?>
	</form>
	
</body>
</html>

