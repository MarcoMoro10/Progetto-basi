<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <link href="viewStats.css?ts=<?=time()?>&quot" rel="stylesheet" type="text/css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>

</head>
<body>
</form>
        <form method="post" action = "Sondaggi.php">
            <input type="submit" id="back" name="back" value='indietro'>
        </form>
<form action = "viewStatistiche.php" method="post">
<?php
session_start();
error_reporting(0);
require_once 'conn.php';

if($_SESSION['domain']==="PREMIUM"){
    if(isset($_POST['visualizza_statistiche_sondaggio'])){
        $codice_sonda=$_POST['visualizza_statistiche_sondaggio'];

        //fare query dove per ogni domanda del sondaggio deve vedere(count) il numero di risposte
        $query="SELECT Codice_Sondaggio,Id_Domanda 
        FROM POSSESSO
        WHERE  Codice_Sondaggio=:codice_sondaggio AND Id_Domanda IN (SELECT Id_Domanda FROM INTERESSAMENTO_2 WHERE Email_Premium =:email_utente ) ";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email_utente', $_SESSION['email']);
        $stmt->bindParam(':codice_sondaggio', $codice_sonda);
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
			$utenti[] = $row;
		}
        echo'<table>';
        echo '<thead>';
		echo	'<tr>';
		echo		'<th>Codice</th>';
        echo        '<th>Domanda Id</th>';
		echo		'<th>Numero Risposte</th>';
		echo	'</tr>';
		echo '</thead>';
		echo'<tbody>';
       
		foreach($utenti as $codice) :
            $query="SELECT count(Codice)  as count FROM RISPOSTA WHERE Id_Domanda=:id_domanda ";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id_domanda',$codice['Id_Domanda'] );
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $result['count'];

			echo'<tr>';
			$input_id =1; 
            echo "<td for= '".$input_id."'>" .$codice['Codice_Sondaggio'].'</td>';
			echo "<td for= '".$input_id."'>" .$codice['Id_Domanda'].'</td>';
			echo "<td for= '".$input_id."'>" .$count.'</td>';   
			echo'</tr>';
		endforeach;	
        
       
        /// cambio di query per provare ad implementare le risposte chiuse
        $queryL="SELECT Codice_Sondaggio,Id_Domanda 
        FROM POSSESSO
        WHERE  Codice_Sondaggio=:codice_sondaggio AND Id_Domanda IN (SELECT Id_Domanda FROM INTERESSAMENTO_2 WHERE Email_Premium =:email_utente ) AND Id_Domanda IN (SELECT Id FROM CHIUSA)";
        $stmt = $conn->prepare($queryL);
        $stmt->bindParam(':email_utente', $_SESSION['email']);
        $stmt->bindParam(':codice_sondaggio', $codice_sonda);
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
			$Codici[] = $row;
		}


        foreach($Codici as $code) :
            // selezionare il numero di righe della stessa risposta per una domanda
            $query="SELECT Testo_Risposta,count(*) as Valore FROM RISPOSTA WHERE Id_Domanda=:id_domand AND Id_Domanda IN (SELECT Id FROM CHIUSA) GROUP BY Testo_Risposta";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id_domand',$code['Id_Domanda'] );
            $stmt->execute();
            
        $data = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[$row['Testo_Risposta']] = $row['Valore'];
        }
        
        
        
        echo '<div style="height: 300px">';
        echo '<canvas id="'.$code['Id_Domanda'].'" width="100" height="100" style="position: relative;"></canvas>';
        echo '</div>';
        echo '<script>';
        echo 'var ctx = document.getElementById("'.$code['Id_Domanda'] . '").getContext("2d");';
        echo 'var myChart = new Chart(ctx, {';
        echo '    type: "bar",';
        echo '    data: {';
        echo '        labels: ' . json_encode(array_keys($data)) . ','; // array stampa il formato indice con anche tipo una freccia, il json rende il formato più leggibile la freccia è sostituita dai due punti
        echo '        datasets: [{';
        echo '            label: "Valori",';
        echo '            data: ' . json_encode(array_values($data)) . ',';// array che stampa il valore quante volte ho rispost
        echo '            backgroundColor: "rgba(0, 255, 0, 0.2)",';
        echo '            borderColor: "rgba(0,255,0,1)",';
        echo '            borderWidth: 1';
        echo '        }]';
        echo '    },';
        echo '    options: {';
        echo ' maintainAspectRatio: false,  ';
        echo '   responsive: true, ';
        echo '        scales: {';
        echo '            yAxes: [{';
        echo '                ticks: {';
        echo '                    beginAtZero:true';
        echo '                }';
        echo '            }]';
        echo '        }';
        //echo '  width:100, ';
        //echo '   height:100 ';
        echo '    }';
        echo '});';
        echo '</script>';

        
        endforeach;
        echo'<br>';
    /// realizzazione implementazione risposte chiuse 
    $queryLog="SELECT Codice_Sondaggio,Id_Domanda 
    FROM POSSESSO
    WHERE  Codice_Sondaggio=:codice_sondaggio AND Id_Domanda IN (SELECT Id_Domanda FROM INTERESSAMENTO_2 WHERE Email_Premium =:email_utente ) AND Id_Domanda IN (SELECT Id FROM APERTA)";
    $stmt = $conn->prepare($queryLog);
    $stmt->bindParam(':email_utente', $_SESSION['email']);
    $stmt->bindParam(':codice_sondaggio', $codice_sonda);
    $stmt->execute();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $Caratteri[] = $row;
    }
    echo'<table>';
        echo '<thead>';
		echo	'<tr>';
		echo		'<th>Domanda</th>';
        echo        '<th>Lunghezza Massima Caratteri</th>';
		echo		'<th>Media Caratteri</th>';
		echo	'</tr>';
		echo '</thead>';
		echo'<tbody>';

        foreach($Caratteri as $code) :
            // selezionare il numero di righe della stessa risposta per una domanda
            $query="SELECT MAX(length(Testo_Risposta)) AS Massimo  , avg(length(Testo_Risposta)) AS Media  FROM RISPOSTA WHERE Id_Domanda=:id_domand ";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id_domand',$code['Id_Domanda'] );
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo'<tr>';
                $input_id =1; 
                echo "<td for= '".$input_id."'>" .$code['Id_Domanda'].'</td>';
                echo "<td for= '".$input_id."'>" .$result['Massimo'].'</td>';
                echo "<td for= '".$input_id."'>" .$result['Media'].'</td>';   
                echo'</tr>';
        endforeach;

    }

}if($_SESSION['domain']==="AZIENDA"){
    if(isset($_POST['visualizza_statistiche_sondaggio'])){
        $codice_sondaggio_azienda=$_POST['visualizza_statistiche_sondaggio'];
        $query="SELECT Codice_Sondaggio,Id_Domanda 
        FROM POSSESSO
        WHERE  Codice_Sondaggio=:codice_sondaggio AND Id_Domanda IN (SELECT Id_Domanda FROM INTERESSAMENTO_1 WHERE Codice_Fiscale_Azienda = :codice_fiscale_azienda ) ";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':codice_fiscale_azienda', $_SESSION['codice']);
        $stmt->bindParam(':codice_sondaggio', $codice_sondaggio_azienda);
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
			$utenti[] = $row;
		}

        echo'<table>';
        echo '<thead>';
		echo	'<tr>';
		echo		'<th>Codice</th>';
        echo        '<th>Domanda Id</th>';
		echo		'<th>Numero Risposte</th>';
		echo	'</tr>';
		echo '</thead>';
		echo'<tbody>';
        
		foreach($utenti as $codice) :
            $query="SELECT count(Codice)  as count FROM RISPOSTA WHERE Id_Domanda=:id_domanda";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id_domanda',$codice['Id_Domanda'] );
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $result['count'];

			echo'<tr>';
			$input_id =1; 
            echo "<td for= '".$input_id."'>" .$codice['Codice_Sondaggio'].'</td>';
			echo "<td for= '".$input_id."'>" .$codice['Id_Domanda'].'</td>';
			echo "<td for= '".$input_id."'>" .$count.'</td>';   
			echo'</tr>';
		endforeach;	

        /// cambio di query per provare ad implementare le risposte chiuse
        $queryL="SELECT Codice_Sondaggio,Id_Domanda 
        FROM POSSESSO
        WHERE  Codice_Sondaggio=:codice_sondaggio AND Id_Domanda IN (SELECT Id_Domanda FROM INTERESSAMENTO_1 WHERE Codice_Fiscale_Azienda = :codice_fiscale_azienda) AND Id_Domanda IN (SELECT Id FROM CHIUSA)";
        $stmt = $conn->prepare($queryL);
        $stmt->bindParam(':codice_fiscale_azienda', $_SESSION['codice']);
        $stmt->bindParam(':codice_sondaggio', $codice_sondaggio_azienda);
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
			$Codici[] = $row;
		}
       

        foreach($Codici as $code) :
            // selezionare il numero di righe della stessa risposta per una domanda
            $query="SELECT Testo_Risposta,count(*) as Valore FROM RISPOSTA WHERE Id_Domanda=:id_domand AND Id_Domanda IN (SELECT Id FROM CHIUSA) GROUP BY Testo_Risposta";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id_domand',$code['Id_Domanda'] );
            $stmt->execute();
            
        $data = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[$row['Testo_Risposta']] = $row['Valore'];
        }
       
       
        echo '<div style="height: 300px">';
        echo '<canvas id="'.$code['Id_Domanda'].'" width="100" height="100" style="position: relative;"></canvas>';
        echo '</div>';
        echo '<script>';
        echo 'var ctx = document.getElementById("'.$code['Id_Domanda'] . '").getContext("2d");';
        echo 'var myChart = new Chart(ctx, {';
        echo '    type: "bar",';
        echo '    data: {';
        echo '        labels: ' . json_encode(array_keys($data)) . ','; // array stampa il formato indice con anche tipo una freccia, il json rende il formato più leggibile la freccia è sostituita dai due punti
        echo '        datasets: [{';
        echo '            label: "Valori",';
        echo '            data: ' . json_encode(array_values($data)) . ',';// array che stampa il valore quante volte ho rispost
        echo '            backgroundColor: "rgba(0, 255, 0, 0.2)",';
        echo '            borderColor: "rgba(0,255,0,1)",';
        echo '            borderWidth: 1';
        echo '        }]';
        echo '    },';
        echo '    options: {';
        echo ' maintainAspectRatio: false,  ';
        echo '   responsive: true, ';
        echo '        scales: {';
        echo '            yAxes: [{';
        echo '                ticks: {';
        echo '                    beginAtZero:true';
        echo '                }';
        echo '            }]';
        echo '        }';
        //echo '  width:100, ';
        //echo '   height:100 ';
        echo '    }';
        echo '});';
        echo '</script>';
        endforeach;
        echo'<br>';

        $queryLog="SELECT Codice_Sondaggio,Id_Domanda 
        FROM POSSESSO
        WHERE  Codice_Sondaggio=:codice_sondaggio AND Id_Domanda IN (SELECT Id_Domanda FROM INTERESSAMENTO_1 WHERE Codice_Fiscale_Azienda =:codice_fiscale_azienda ) AND Id_Domanda IN (SELECT Id FROM APERTA)";
        $stmt = $conn->prepare($queryLog);
        $stmt->bindParam(':codice_fiscale_azienda', $_SESSION['codice']);
        $stmt->bindParam(':codice_sondaggio', $codice_sondaggio_azienda);
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $Caratteri[] = $row;
        }
        echo'<table>';
            echo '<thead>';
            echo	'<tr>';
            echo		'<th>Domanda</th>';
            echo        '<th>Lunghezza Massima Caratteri</th>';
            echo		'<th>Media Caratteri</th>';
            echo	'</tr>';
            echo '</thead>';
            echo'<tbody>';
    
            foreach($Caratteri as $code) :
                // selezionare il numero di righe della stessa risposta per una domanda
                $query="SELECT MAX(length(Testo_Risposta)) AS Massimo  , avg(length(Testo_Risposta)) AS Media  FROM RISPOSTA WHERE Id_Domanda=:id_domand ";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':id_domand',$code['Id_Domanda'] );
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo'<tr>';
                    $input_id =1; 
                    echo "<td for= '".$input_id."'>" .$code['Id_Domanda'].'</td>';
                    echo "<td for= '".$input_id."'>" .$result['Massimo'].'</td>';
                    echo "<td for= '".$input_id."'>" .$result['Media'].'</td>';   
                    echo'</tr>';
            endforeach;

    }
}


    ?>

   
    </body>
    </html>