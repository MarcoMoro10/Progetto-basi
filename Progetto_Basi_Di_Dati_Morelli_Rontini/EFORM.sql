DROP DATABASE IF EXISTS EFORM;
CREATE DATABASE IF NOT EXISTS EFORM;
SET GLOBAL event_scheduler = ON;
Use EFORM;

CREATE TABLE AZIENDA (
	Codice_Fiscale varchar(250) primary key,
	Email varchar(250),
    Password CHAR(64) NOT NULL,
    Nome varchar(250),
	Sede varchar(250)
)ENGINE="InnoDB";

CREATE TABLE UTENTE (
	Email varchar(60) NOT NULL primary key,
	Password CHAR(64) NOT NULL,
    Nome varchar(250),
	Cognome varchar(250),
    Luogo_Nascita varchar(250),
	Anno_Nascita date,
	Totale_Bonus DOUBLE default 0
)ENGINE="InnoDB";

CREATE TABLE PREMIUM (
	Email varchar(250) primary KEY,
	Costo int,
	Inizio_Abbonamento date,
	Fine_Abbonamento date,
	Numero_Sondaggi int DEFAULT 0,
	KEY FK_Utente(Email),
    constraint FK_utente FOREIGN KEY(Email) references UTENTE(Email) ON DELETE CASCADE
)ENGINE="InnoDB";

CREATE TABLE AMMINISTRATORE (
	Email varchar(250) NOT NULL primary KEY,
	KEY FK_utente_amm(Email),
	constraint FK_utente_amm FOREIGN KEY(Email) references UTENTE(Email) ON DELETE CASCADE
)ENGINE="InnoDB";

CREATE TABLE DOMINIO (
	Parola varchar(250) primary KEY,
	Descrizione varchar(250),
    Email_Utente_Amm varchar(250),
	key FK_utente_amministrazione(Email_utente_Amm),
	constraint FK_utente_amministrazione foreign key(Email_utente_Amm) references AMMINISTRATORE(Email) ON DELETE CASCADE
)ENGINE="InnoDB";

CREATE TABLE SONDAGGIO (
	Codice varchar(250) primary KEY,
	Stato enum('APERTO', 'CHIUSO'),
	Titolo varchar(250),
	Data_Chiusura date,
	Data_Apertura date,
	Max_Utenti int,
    Dominio_Parola varchar(250),
	Data_Creazione date,
    KEY FK_Dominio_Parola (Dominio_Parola),
    CONSTRAINT FK_Dominio_Parola FOREIGN KEY(Dominio_Parola) references DOMINIO(Parola) ON DELETE CASCADE
)ENGINE="InnoDB";


CREATE TABLE PREMI_DISPONIBILI (
	Codice varchar(250) primary KEY,
	Nome varchar(250),
	Foto varchar(250),
	Descrizione varchar(250),
    Minimo_Punti int,
    Email_Amministratore varchar(250),
	KEY FK_utente_Amministratore(Email_Amministratore),
	constraint FK_utente_Amministratore FOREIGN KEY(Email_Amministratore) references AMMINISTRATORE(Email) ON DELETE CASCADE
)ENGINE="InnoDB";

CREATE TABLE VINCITA (
	Email_Utente varchar(250),
    Codice_Premio varchar(250),
    primary key(Email_Utente,Codice_Premio),
    KEY FK_Utente_U(Email_Utente),
    KEY FK_Codice_Premio(Codice_Premio),
    constraint FK_Utente_U FOREIGN KEY(Email_utente) references UTENTE(Email) ON DELETE CASCADE,
    constraint FK_Codice_Premio FOREIGN KEY(Codice_Premio) references PREMI_DISPONIBILI(Codice) ON DELETE CASCADE
    )ENGINE="InnoDB";
    
CREATE TABLE INVITO (
    Codice varchar(250) primary key,
    Esito varchar(250),
    Email_Utente varchar(250),
    Codice_Sondaggio varchar(250),
    KEY FK_Invito_Email_Utente(Email_Utente),
    KEY FK_Invito_Codice_Sondaggio(Codice_Sondaggio),
	CONSTRAINT FK_Invito_Email_Utente FOREIGN KEY(Email_Utente) references UTENTE(Email) ON DELETE CASCADE,
	CONSTRAINT FK_Invito_Codice_Sondaggio FOREIGN KEY(Codice_Sondaggio) references SONDAGGIO(Codice) ON DELETE CASCADE
)ENGINE="InnoDB";

CREATE TABLE DOMANDA (
	Id int auto_increment primary key,
	Testo varchar(250),
	Punteggio int,
	Foto varchar(1000)
)ENGINE="InnoDB";

CREATE TABLE RISPOSTA (
	Codice varchar(250) primary key,
    Testo_Risposta varchar(250),
    Id_Domanda int,
	Email_Utente varchar(250),
    KEY FK_Email_Utente(Email_Utente),
    KEY FK_Id_Domanda_Risposta(Id_Domanda),
    CONSTRAINT FK_Email_Utente FOREIGN KEY(Email_Utente) references UTENTE(Email) ON DELETE CASCADE,
    CONSTRAINT FK_Id_Domanda_Risposta FOREIGN KEY(Id_Domanda) references DOMANDA(Id) ON DELETE CASCADE
)ENGINE="InnoDB";

CREATE TABLE APERTA (
	Id int primary key,
    Max_Caratteri int,
    KEY FK_Id_Domanda(Id),
    CONSTRAINT FK_Id_Domanda FOREIGN KEY(Id) references DOMANDA(Id) ON DELETE CASCADE
)ENGINE="InnoDB";

CREATE TABLE CHIUSA (
	Id int primary key,
    KEY FK_Id_Domanda_Chiusa(Id),
    CONSTRAINT FK_Id_Domanda_Chiusa FOREIGN KEY(Id) references DOMANDA(Id) ON DELETE CASCADE
)ENGINE="InnoDB";

CREATE TABLE OPZIONE (
	Id int,
	Numero int,
	Testo varchar(250),
    primary key(Id,Numero),
	KEY FK_Id_Domanda_Chiusa_Opzione(Id),
	CONSTRAINT FK_Id_Domanda_Chiusa_Opzione FOREIGN KEY(Id) references CHIUSA(Id) ON DELETE CASCADE
)ENGINE="InnoDB";

CREATE TABLE POSSESSO (
	Id_Domanda int,
	Codice_Sondaggio varchar(250),
    KEY FK_Id_Domanda_Interessamento(Id_Domanda),
    KEY FK_Id_Codice_Sondaggio(Codice_Sondaggio),
    CONSTRAINT FK_Id_Domanda_Interessamento FOREIGN KEY(Id_Domanda) references DOMANDA(Id) ON DELETE CASCADE,
    CONSTRAINT FK_Id_Codice_Sondaggio FOREIGN KEY(Codice_Sondaggio) references SONDAGGIO(Codice) ON DELETE CASCADE,
    primary key(Id_Domanda,Codice_Sondaggio)
)ENGINE="InnoDB";

CREATE TABLE INTERESSAMENTO (
	Email_Utente varchar(250),
	Dominio_Parola varchar(250),
    KEY FK_Id_Email_Utente(Email_Utente),
    KEY FK_Id_Dominio_Parola(Dominio_Parola),
    CONSTRAINT FK_Id_Email_Utente FOREIGN KEY(Email_Utente) references UTENTE(Email) ON DELETE CASCADE,
    CONSTRAINT FK_Id_Dominio_Parola FOREIGN KEY(Dominio_Parola) references DOMINIO(Parola) ON DELETE CASCADE,
    primary key(Email_Utente,Dominio_Parola)
)ENGINE="InnoDB";

CREATE TABLE RISP_APERTA (
	Codice varchar(250) primary key,
    Max_Caratteri int,
    KEY FK_Id_Codice_Aperta(Codice),
    CONSTRAINT FK_Id_Codice_Aperta FOREIGN KEY(Codice) references RISPOSTA(Codice) ON DELETE CASCADE
)ENGINE="InnoDB";

CREATE TABLE RISP_CHIUSA (
	 Codice varchar(250) primary key,
     KEY FK_Id_Codice_Chiusa(Codice),
    CONSTRAINT FK_Id_Codice_Chiusa FOREIGN KEY(Codice) references RISPOSTA(Codice) ON DELETE CASCADE
)ENGINE="InnoDB";


CREATE TABLE INTERESSAMENTO_1 (
	 Id_Domanda int,
     Codice_Fiscale_Azienda varchar(250),
     primary key(Id_Domanda,Codice_Fiscale_Azienda),
     KEY FK_Id_INTERESSAMENTO_1(Id_Domanda),
     KEY FK_CODICE_FISCALE_INTERESSAMENTO_1(Codice_Fiscale_Azienda),
     CONSTRAINT FK_CODICE_FISCALE_INTERESSAMENTO_1 FOREIGN KEY(Codice_Fiscale_Azienda) references AZIENDA(Codice_Fiscale) ON DELETE CASCADE,
     CONSTRAINT FK_Id_INTERESSAMENTO_1 FOREIGN KEY(Id_Domanda) references DOMANDA(Id) ON DELETE CASCADE
)ENGINE="InnoDB";

CREATE TABLE INTERESSAMENTO_2 (
	 Id_Domanda int,
     Email_Premium varchar(250),
     primary key(Id_Domanda,Email_Premium),
     KEY FK_Id_INTERESSAMENTO_2(Id_Domanda),
     KEY FK_CODICE_EMAIL_INTERESSAMENTO_2(Email_Premium),
     CONSTRAINT FK_EMAIL_INTERESSAMENTO_2 FOREIGN KEY(Email_Premium) references PREMIUM(Email) ON DELETE CASCADE,
     CONSTRAINT FK_Id_INTERESSAMENTO_2 FOREIGN KEY(Id_Domanda) references DOMANDA(Id) ON DELETE CASCADE
)ENGINE="InnoDB";

CREATE TABLE CREAZIONE_1 (
	 Codice_Sondaggio varchar(250),
     Codice_Fiscale_Azienda varchar(250),
     primary key(Codice_Sondaggio,Codice_Fiscale_Azienda),
     KEY FK_Id_CREAZIONE_1(Codice_Sondaggio),
     KEY FK_CODICE_FISCALE_CREAZIONE_1(Codice_Fiscale_Azienda),
     CONSTRAINT FK_CODICE_FISCALE_CREAZIONE_1 FOREIGN KEY(Codice_Fiscale_Azienda) references AZIENDA(Codice_Fiscale) ON DELETE CASCADE,
     CONSTRAINT FK_Id_CREAZIONE_1 FOREIGN KEY(Codice_Sondaggio) references SONDAGGIO(Codice) ON DELETE CASCADE
)ENGINE="InnoDB";

CREATE TABLE CREAZIONE_2 (
	 Codice_Sondaggio varchar(250),
     Email_Premium varchar(250),
     primary key(Codice_Sondaggio,Email_Premium),
     KEY FK_Id_CREAZIONE_2(Codice_Sondaggio),
     KEY FK_EMAIL_CREAZIONE_2(Email_Premium),
     CONSTRAINT FK_EMAIL_CREAZIONE_2 FOREIGN KEY(Email_Premium) references PREMIUM(Email) ON DELETE CASCADE,
     CONSTRAINT FK_Id_CREAZIONE_2 FOREIGN KEY(Codice_Sondaggio) references SONDAGGIO(Codice) ON DELETE CASCADE
)ENGINE="InnoDB";

CREATE TABLE INVIATO (
	 Codice_Invito varchar(250),
     Email_Premium varchar(250),
     primary key(Codice_Invito,Email_Premium),
     KEY FK_Id_INVIATO_CODICE(Codice_Invito),
     KEY FK_EMAIL_INVIATO(Email_Premium),
     CONSTRAINT FK_EMAIL_INVIATO FOREIGN KEY(Email_Premium) references PREMIUM(Email) ON DELETE CASCADE,
     CONSTRAINT FK_Id_INVIATO_CODICE FOREIGN KEY(Codice_Invito) references INVITO(Codice) ON DELETE CASCADE
)ENGINE="InnoDB";

CREATE TABLE SPEDISCI (
	 Codice_Spedisci_Invito varchar(250),
     Codice_Fiscale_Azienda varchar(250),
     primary key(Codice_Spedisci_Invito, Codice_Fiscale_Azienda),
     KEY FK_Id_SPEDISCI_CODICE_INVITO(Codice_Spedisci_Invito),
     KEY FK_SPEDISCI_CODICE_FISCALE_AZIENDA(Codice_Fiscale_Azienda),
     CONSTRAINT FK_SPEDISCI_CODICE_FISCALE_AZIENDA FOREIGN KEY(Codice_Fiscale_Azienda) references AZIENDA(Codice_Fiscale) ON DELETE CASCADE,
     CONSTRAINT FK_Id_SPEDISCI_CODICE_INVITO FOREIGN KEY(Codice_Spedisci_Invito) references INVITO(Codice) ON DELETE CASCADE
)ENGINE="InnoDB";

CREATE TABLE NOTE (
	 Codice varchar(250) primary key,
     Testo varchar(250),
     Email_Utente varchar(250),
     KEY FK_Note_Utente(Email_Utente),
     CONSTRAINT FK_Note_Utente FOREIGN KEY(Email_Utente) references UTENTE(Email) ON DELETE CASCADE
)ENGINE="InnoDB";


/*STORED PROCEDURE*/
DELIMITER |
CREATE PROCEDURE Inserisci_UTENTE
(IN Email_Utente varchar(250), Password_Utente varchar(250), Nome_Utente varchar(250), Cognome_Utente varchar(250), Lougo_Nascita_Utente varchar(250), Anno_Nascita_Utente date)
BEGIN
		declare noAzienda varchar(250);
		SET noAzienda =(SELECT count(*) FROM AZIENDA where Email=Email_Utente); /*Controllo che l'email dell'Utente non sia un email dell'Azienda*/
        IF (noAzienda = 0) THEN
			INSERT INTO UTENTE(Email,Password,Nome,Cognome,Luogo_Nascita,Anno_Nascita) VALUES (Email_Utente,sha2(Password_Utente, 256), Nome_Utente, Cognome_Utente , Lougo_Nascita_Utente, Anno_Nascita_Utente);
		END IF;
END;
|
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserisci_PREMIUM
(IN Email_Utente varchar(250), Costo_Premium integer(250), Inizio_Abbonamento_Premium date, Fine_Abbonamento_Premium date)
BEGIN	
		declare noAmministratore varchar(250);
		declare noPremium varchar(250);
		SET noAmministratore =(SELECT count(*) FROM AMMINISTRATORE where Email=Email_Utente); /*Controllo che l'Utente che diventa Premium non sia Amministratore*/
		SET noPremium =(SELECT count(*) FROM PREMIUM where Email=Email_Utente); /*Controllo che l'Utente che diventa Premium non sia già premium*/
        IF (noAmministratore = 0 && noPremium = 0) THEN
			INSERT INTO PREMIUM(Email,Costo,Inizio_Abbonamento,Fine_Abbonamento) VALUES (Email_Utente,Costo_Premium,Inizio_Abbonamento_Premium,Fine_Abbonamento_Premium);
        END IF;
        	
END;
|
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserisci_AMMINISTRATORE
(IN Email_Utente varchar(250))
BEGIN
		declare noPremium varchar(250);
		SET noPremium =(SELECT count(*) FROM PREMIUM where Email=Email_Utente); /*Controllo che l'Utente che diventa Amministratore non sia Premium*/
        IF (noPremium = 0) THEN
			INSERT INTO AMMINISTRATORE(Email) VALUES (Email_Utente);
        END IF;
END;
|
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserisci_AZIENDA
(IN Azienda_Codice_Fiscale varchar(250),Email_Azienda varchar(250), Password_Azienda varchar(250),Nome_Azienda varchar(250), Sede_Azienda varchar(250))
BEGIN
		declare noUtente varchar(250);
		SET noUtente =(SELECT count(*) FROM UTENTE where Email=Email_Azienda); /*Controllo che l'email dell'Azienda non sia un email di un Utente*/
        IF (noUtente = 0) THEN
			INSERT INTO AZIENDA(Codice_Fiscale,Email,Password,Nome,Sede) VALUES (Azienda_Codice_Fiscale,Email_Azienda,sha2(Password_Azienda, 256),Nome_Azienda,Sede_Azienda);
        END IF;
END;
|
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserisci_DOMINIO
(IN Parola_Dominio varchar(250),Descrizione_Dominio varchar(250), Email_Utente_Amm_Dominio varchar(250))
BEGIN
	INSERT INTO DOMINIO(Parola,Descrizione,Email_Utente_Amm) VALUES (Parola_Dominio,Descrizione_Dominio,Email_Utente_Amm_Dominio);
END;
|
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserisci_SONDAGGIO_PREMIUM
(IN Codice_Sondaggio_Premium varchar(250),Stato_Sondaggio varchar(250),Titolo_Sondaggio varchar(250), Data_Chiusura_Sondaggio datetime, Data_Apertura_Sondaggio datetime, Max_Utenti_Sondaggio integer(250), Parola_Sondaggio varchar(250),Email_Utente_Premium_Sondaggio varchar(250))
BEGIN
	INSERT INTO SONDAGGIO(Codice,Stato,Titolo,Data_Chiusura,Data_Apertura,Max_Utenti,Dominio_Parola,Data_Creazione) VALUES (Codice_Sondaggio_Premium,Stato_Sondaggio,Titolo_Sondaggio,Data_Chiusura_Sondaggio,Data_Apertura_Sondaggio,Max_Utenti_Sondaggio,Parola_Sondaggio,now());
    INSERT INTO CREAZIONE_2(Codice_Sondaggio,Email_Premium) VALUES(Codice_Sondaggio_Premium,Email_Utente_Premium_Sondaggio);
END;
|
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserisci_SONDAGGIO_AZIENDA
(IN Codice_Sondaggio_Azienda varchar(250),Stato_Sondaggio varchar(250),Titolo_Sondaggio varchar(250), Data_Chiusura_Sondaggio datetime, Data_Apertura_Sondaggio datetime, Max_Utenti_Sondaggio integer(250), Parola_Sondaggio varchar(250),Codice_Utente_Azienda_Sondaggio varchar(250))
BEGIN
	INSERT INTO SONDAGGIO(Codice,Stato,Titolo,Data_Chiusura,Data_Apertura,Max_Utenti,Dominio_Parola,Data_Creazione) VALUES (Codice_Sondaggio_Azienda,Stato_Sondaggio,Titolo_Sondaggio,Data_Chiusura_Sondaggio,Data_Apertura_Sondaggio,Max_Utenti_Sondaggio,Parola_Sondaggio,now());
    INSERT INTO CREAZIONE_1(Codice_Sondaggio,Codice_Fiscale_Azienda) VALUES(Codice_Sondaggio_Azienda,Codice_Utente_Azienda_Sondaggio);
END;
|
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserisci_DOMANDA_APERTA_PREMIUM
(IN Testo_Domanda_Aperta varchar(250),Punteggio_Domanda_Aperta varchar(250), Foto_Domanda_Aperta varchar(1000), Max_Caratteri_Domanda_Aperta varchar(250), Email_Premium_Domanda varchar(250), Codice_Sondaggio varchar(250), OUT Nuovo_ID int)
BEGIN
	IF CHAR_LENGTH(Testo_Domanda_Aperta) < Max_Caratteri_Domanda_Aperta THEN
        INSERT INTO DOMANDA(Testo,Punteggio,Foto) VALUES(Testo_Domanda_Aperta,Punteggio_Domanda_Aperta,Foto_Domanda_Aperta);
		SET Nuovo_ID = LAST_INSERT_ID();
        INSERT INTO APERTA(Id,Max_Caratteri) VALUES(Nuovo_ID,Max_Caratteri_Domanda_Aperta);
		INSERT INTO INTERESSAMENTO_2(Id_Domanda,Email_Premium) VALUES(Nuovo_ID,Email_Premium_Domanda);
		INSERT INTO POSSESSO(Id_Domanda,Codice_Sondaggio) VALUES(Nuovo_ID,Codice_Sondaggio);
		SELECT Nuovo_ID AS NewID;
    END IF;
END;
|
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserisci_DOMANDA_CHIUSA_PREMIUM
(IN Testo_Domanda_Chiusa varchar(250),Punteggio_Domanda_Chiusa varchar(250), Foto_Domanda_Chiusa varchar(1000), Email_Premium_Domanda varchar(250), Codice_Sondaggio varchar(250), OUT Nuovo_ID int)
BEGIN
	INSERT INTO DOMANDA(Testo,Punteggio,Foto) VALUES(Testo_Domanda_Chiusa,Punteggio_Domanda_Chiusa,Foto_Domanda_Chiusa);
    SET Nuovo_ID = LAST_INSERT_ID();
	INSERT INTO CHIUSA(Id) VALUES(Nuovo_ID);
    INSERT INTO INTERESSAMENTO_2(Id_Domanda,Email_Premium) VALUES(Nuovo_ID,Email_Premium_Domanda);
	INSERT INTO POSSESSO(Id_Domanda,Codice_Sondaggio) VALUES(Nuovo_ID,Codice_Sondaggio);
	SELECT Nuovo_ID AS NewID;
END;
|
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserisci_DOMANDA_APERTA_AZIENDA
(IN Testo_Domanda_Aperta varchar(250),Punteggio_Domanda_Aperta varchar(250), Foto_Domanda_Aperta varchar(1000), Max_Caratteri_Domanda_Aperta varchar(250), Codice_Fiscale_Azienda_Domanda varchar(250), Codice_Sondaggio varchar(250), OUT Nuovo_ID int)
BEGIN
	IF CHAR_LENGTH(Testo_Domanda_Aperta) < Max_Caratteri_Domanda_Aperta THEN
        INSERT INTO DOMANDA(Testo,Punteggio,Foto) VALUES(Testo_Domanda_Aperta,Punteggio_Domanda_Aperta,Foto_Domanda_Aperta);
		SET Nuovo_ID = LAST_INSERT_ID();
        INSERT INTO APERTA(Id,Max_Caratteri) VALUES(Nuovo_ID,Max_Caratteri_Domanda_Aperta);
		INSERT INTO INTERESSAMENTO_1(Id_Domanda,Codice_Fiscale_Azienda) VALUES(Nuovo_ID,Codice_Fiscale_Azienda_Domanda);
		INSERT INTO POSSESSO(Id_Domanda,Codice_Sondaggio) VALUES(Nuovo_ID,Codice_Sondaggio);
		SELECT Nuovo_ID AS NewID;
    END IF;
END;
|
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserisci_DOMANDA_CHIUSA_AZIENDA
(IN Testo_Domanda_Chiusa varchar(250),Punteggio_Domanda_Chiusa varchar(250), Foto_Domanda_Chiusa varchar(1000), Codice_Fiscale_Azienda_Domanda varchar(250), Codice_Sondaggio varchar(250), OUT Nuovo_ID int)
BEGIN
	INSERT INTO DOMANDA(Testo,Punteggio,Foto) VALUES(Testo_Domanda_Chiusa,Punteggio_Domanda_Chiusa,Foto_Domanda_Chiusa);
    SET Nuovo_ID = LAST_INSERT_ID();
	INSERT INTO CHIUSA(Id) VALUES(Nuovo_ID);
    INSERT INTO INTERESSAMENTO_1(Id_Domanda,Codice_Fiscale_Azienda) VALUES(Nuovo_ID,Codice_Fiscale_Azienda_Domanda);
	INSERT INTO POSSESSO(Id_Domanda,Codice_Sondaggio) VALUES(Nuovo_ID,Codice_Sondaggio);
	SELECT Nuovo_ID AS NewID;
END;
|
DELIMITER ;

|
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserisci_DOMANDA_CHIUSA_OPZIONE
(IN Id_Domanda_Chiusa varchar(250),Numero_Opzione int,Testo_Opzione varchar(250))
BEGIN
	INSERT INTO OPZIONE(Id,Numero,Testo) VALUES(Id_Domanda_Chiusa,Numero_Opzione,Testo_Opzione);
END;

|
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserisci_INTERESSAMENTO
(IN Email_Utente_Interessamento varchar(250), Dominio_Parola_Interessamento varchar(250))
BEGIN
	INSERT INTO INTERESSAMENTO(Email_Utente,Dominio_Parola) VALUES(Email_Utente_Interessamento,Dominio_Parola_Interessamento);
END;
|
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Elimina_INTERESSAMENTO
(IN Email_Utente_Interessamento varchar(250), Dominio_Parola_Interessamento varchar(250))
BEGIN
	DELETE FROM INTERESSAMENTO WHERE Email_Utente = Email_Utente_Interessamento AND Dominio_Parola = Dominio_Parola_Interessamento;
END;
|
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserisci_RISPOSTA_CHIUSA
(IN Codice_Risposta_Chiusa varchar(250), Testo_Risposta varchar(250), Email_Utente_Risposta varchar(250), Id_Domanda_Chiusa varchar(250))
BEGIN
	INSERT INTO RISPOSTA(Codice,Testo_Risposta,Id_Domanda,Email_Utente) VALUES(Codice_Risposta_Chiusa,Testo_Risposta,Id_Domanda_Chiusa,Email_Utente_Risposta);
	INSERT INTO RISP_CHIUSA(Codice) VALUES(Codice_Risposta_Chiusa);
END;

|
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserisci_RISPOSTA_APERTA
(IN Codice_Risposta_Aperta varchar(250), Testo_Risposta varchar(250), Email_Utente_Risposta varchar(250), Id_Domanda_Aperta varchar(250), Max_Caratteri_Aperta int)
BEGIN
	IF CHAR_LENGTH(Testo_Risposta) < Max_Caratteri_Aperta THEN
		INSERT INTO RISPOSTA(Codice,Testo_Risposta,Id_Domanda,Email_Utente) VALUES(Codice_Risposta_Aperta,Testo_Risposta,Id_Domanda_Aperta,Email_Utente_Risposta);
		INSERT INTO RISP_APERTA(Codice,Max_Caratteri) VALUES(Codice_Risposta_Aperta,Max_Caratteri_Aperta);
    END IF;
END;

|
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserisci_INVITO_PREMIUM
(IN Codice_Invito_Premium varchar(250), Esito_Invito varchar(250), Email_Utente_Invito varchar(250), Email_Utente_Premium_Invito varchar(250), Codice_Sondaggio_Invito varchar(250))
BEGIN
    INSERT INTO INVITO(Codice,Esito,Email_Utente,Codice_Sondaggio) VALUES(Codice_Invito_Premium, Esito_Invito, Email_Utente_Invito, Codice_Sondaggio_Invito);
	INSERT INTO INVIATO(Codice_Invito,Email_Premium) VALUES(Codice_Invito_Premium,Email_Utente_Premium_Invito);
END;

|
DELIMITER ;


DELIMITER |
CREATE PROCEDURE Inserisci_INVITO_AZIENDA
(IN Codice_Invito varchar(250), Esito_Invito varchar(250), Email_Utente_Invito varchar(250), Codice_Fiscale_Azienda_Invito varchar(250), Codice_Sondaggio_Invito varchar(250))
BEGIN
    INSERT INTO INVITO(Codice,Esito,Email_Utente,Codice_Sondaggio) VALUES(Codice_Invito, Esito_Invito, Email_Utente_Invito, Codice_Sondaggio_Invito);
	INSERT INTO SPEDISCI(Codice_Spedisci_Invito,Codice_Fiscale_Azienda) VALUES(Codice_Invito, Codice_Fiscale_Azienda_Invito);
END;

|
DELIMITER ;

DELIMITER |
CREATE PROCEDURE Inserisci_PREMI_DISPONIBILI
(IN Codice_Premio varchar(250), Email_Amministratore_Premio varchar(250), Nome_Premio varchar(250), Foto_Premio varchar(1000), Descrizione_Premio varchar(250),Minimo_Punti_Premio integer(250))
BEGIN
    INSERT INTO PREMI_DISPONIBILI(Codice,Nome,Foto,Descrizione, Minimo_Punti,Email_Amministratore) VALUES(Codice_Premio, Nome_Premio, Foto_Premio, Descrizione_Premio, Minimo_Punti_Premio,Email_Amministratore_Premio);
END;

|
DELIMITER ;


DELIMITER |
CREATE PROCEDURE Inserisci_VINCITA_PREMI
(IN  Email_Utente_Vincita_Premio varchar(250), Codice_Premio_Vincita varchar(250))
BEGIN
    INSERT INTO Vincita(Email_Utente,Codice_Premio) VALUES(Email_Utente_Vincita_Premio,Codice_Premio_Vincita);
END;

|
DELIMITER ;
/*TRIGGER 1*/
DELIMITER |
CREATE TRIGGER cambio_stato_sondaggi
AFTER UPDATE ON INVITO
FOR EACH ROW
BEGIN
	DECLARE Numero_Partecipanti_Risposta int;
	DECLARE Data_Chiusura_Trigger date;
    DECLARE Max_Utenti_Trigger int;
	SET Numero_Partecipanti_Risposta = (SELECT count(*) FROM INVITO WHERE Esito = "TRUE" AND Codice_Sondaggio = NEW.Codice_Sondaggio);
	SET Data_Chiusura_Trigger = (SELECT Data_Chiusura FROM SONDAGGIO WHERE Codice = NEW.Codice_Sondaggio);
	SET Max_Utenti_Trigger = (SELECT Max_Utenti FROM SONDAGGIO WHERE SONDAGGIO.Codice = NEW.Codice_Sondaggio);
   IF (Data_Chiusura_Trigger < NOW() OR Numero_Partecipanti_Risposta >= Max_Utenti_Trigger) THEN
      UPDATE SONDAGGIO SET Stato = 'CHIUSO' WHERE Codice = NEW.Codice_Sondaggio;
   END IF;

END;
|
DELIMITER ;

/*TRIGGER 2*/
DELIMITER |
CREATE TRIGGER assegna_premio_principiante
AFTER UPDATE ON UTENTE
FOR EACH ROW
BEGIN
	DECLARE Numero_Bonus int;
	DECLARE Check_Premio int;
    DECLARE Numero_Minimo int;
	SET Numero_Bonus = (SELECT Totale_Bonus FROM UTENTE WHERE Email = NEW.Email);
    SET Check_Premio = (SELECT count(*) FROM VINCITA WHERE Codice_Premio = "1" AND Email_Utente = New.Email);
	SET Numero_Minimo = (SELECT Minimo_Punti FROM PREMI_DISPONIBILI WHERE Codice ="1");
   IF (Numero_Bonus >= Numero_Minimo AND Check_Premio < 1) THEN
      INSERT INTO VINCITA(Email_Utente,Codice_Premio) VALUES(NEW.Email,"1");
   END IF;

END;
|
DELIMITER ;

/*TRIGGER 3*/
DELIMITER |
CREATE TRIGGER assegna_premio_intermedio
AFTER UPDATE ON UTENTE
FOR EACH ROW
BEGIN
	DECLARE Numero_Bonus int;
    DECLARE Check_Premio int;
    DECLARE Numero_Minimo int;
	SET Numero_Bonus = (SELECT Totale_Bonus FROM UTENTE WHERE Email = NEW.Email);
    SET Check_Premio = (SELECT count(*) FROM VINCITA WHERE Codice_Premio = "2" AND Email_Utente = New.Email);
	SET Numero_Minimo = (SELECT Minimo_Punti FROM PREMI_DISPONIBILI WHERE Codice ="2");
   IF (Numero_Bonus >= Numero_Minimo AND Check_Premio < 1) THEN
      INSERT INTO VINCITA(Email_Utente,Codice_Premio) VALUES(NEW.Email,"2");
   END IF;

END;
|
DELIMITER ;

/*TRIGGER 4*/
DELIMITER |
CREATE TRIGGER assegna_premio_maestro
AFTER UPDATE ON UTENTE
FOR EACH ROW
BEGIN
	DECLARE Numero_Bonus int;
    DECLARE Check_Premio int;
    DECLARE Numero_Minimo int;
	SET Numero_Bonus = (SELECT Totale_Bonus FROM UTENTE WHERE Email = NEW.Email);
    SET Check_Premio = (SELECT count(*) FROM VINCITA WHERE Codice_Premio = "3" AND Email_Utente = New.Email);
	SET Numero_Minimo = (SELECT Minimo_Punti FROM PREMI_DISPONIBILI WHERE Codice ="3");
   IF (Numero_Bonus >= Numero_Minimo AND Check_Premio < 1) THEN
      INSERT INTO VINCITA(Email_Utente,Codice_Premio) VALUES(NEW.Email,"3");
   END IF;

END;
|
DELIMITER ;

/*TRIGGER 5*/
DELIMITER |
CREATE TRIGGER IncrementaNumeroSondaggi
AFTER INSERT 
ON CREAZIONE_2 FOR EACH ROW
BEGIN
	UPDATE PREMIUM SET Numero_Sondaggi = Numero_Sondaggi + 1 WHERE (Email = NEW.Email_Premium);
END;
|
DELIMITER ;

/*TRIGGER 6*/
DELIMITER |
CREATE TRIGGER IncrementaTotaleBonus
AFTER UPDATE 
ON INVITO FOR EACH ROW
BEGIN
	UPDATE UTENTE SET Totale_Bonus = Totale_Bonus + 0.5 WHERE (Email = NEW.Email_Utente) AND NEW.Email_Utente IN (SELECT Email_Utente 
    FROM INVITO WHERE Esito='accepted');
END;
|
DELIMITER ;

CREATE EVENT elimina_premium_scaduti
ON SCHEDULE EVERY 1 DAY
DO
  DELETE FROM PREMIUM WHERE Fine_Abbonamento < CURDATE();

CREATE EVENT chiudi_sondaggi_scaduti
ON SCHEDULE EVERY 1 DAY
DO
  UPDATE SONDAGGIO SET Stato = 'CHIUSO' WHERE Data_Chiusura < CURDATE();

CREATE EVENT apri_sondaggi_chiusi
ON SCHEDULE EVERY 1 DAY
DO
  UPDATE SONDAGGIO SET Stato = 'APERTO' WHERE Data_Apertura > CURDATE();


CALL Inserisci_Utente("rossi@gmail.com","ciao","riccardo","rossi","benevento",'2000-12-12');
CALL Inserisci_Utente("maurizio@gmail.com","ciao","maurizio","rossi","ponticelli",'1989-12-12');
CALL Inserisci_Utente("rontinim@gmail.com","ciao","matteo","rontini","imola",'2001-08-14');
CALL Inserisci_Utente("marco@gmail.com","123","marco","morelli","imola",'2001-08-10');
CALL Inserisci_PREMIUM("marco@gmail.com",0,'2023-03-15','2025-03-30');
CALL Inserisci_AMMINISTRATORE("rontinim@gmail.com");
CALL Inserisci_AZIENDA("ssss","ciao@ciao","ciao","ciao.srl","catania");
CALL Inserisci_AZIENDA("LCEOSML","luca@eform.srl","ciao","Eform","Lugano");
CALL Inserisci_DOMINIO("Generale","Categoria di default","rontinim@gmail.com");
CALL Inserisci_DOMINIO("Cinema","Categoria sul cinema","rontinim@gmail.com");
CALL Inserisci_DOMINIO("Sport","Categoria sullo sport","rontinim@gmail.com");
CALL Inserisci_SONDAGGIO_PREMIUM("Prima Guerra","Aperto","Prima Guerra Mondiale",'2023-04-30','2018-06-01',2,"Generale","marco@gmail.com");
CALL Inserisci_SONDAGGIO_PREMIUM("Ultimi Film","Aperto","Ultimi film usciti",'2023-03-30','2022-12-12',20,"Cinema","marco@gmail.com");
CALL Inserisci_SONDAGGIO_PREMIUM("Film Vecchi","Aperto","Film usciti nel passato",'2023-08-30','2018-12-12',20,"Cinema","marco@gmail.com");
CALL Inserisci_SONDAGGIO_PREMIUM("Champions League","Aperto","Vittoria Champions League",'2024-04-1','2023-03-23',30,"Sport","marco@gmail.com");
CALL Inserisci_SONDAGGIO_AZIENDA("Elezioni","Aperto","Elezioni USA",'2023-06-1','2023-01-12',2,"Generale","ssss");
CALL Inserisci_SONDAGGIO_AZIENDA("Attori","Aperto","Attori Holywood",'2028-12-12','2021-07-07',10,"Cinema","ssss");
CALL Inserisci_SONDAGGIO_AZIENDA("Europa League","Aperto","Giocatori",'2023-06-17','2022-09-27',20,"Sport","ssss");
CALL Inserisci_DOMANDA_APERTA_PREMIUM("Cosa ne pensi della prima guerra mondiale?",1,"https://cdn.skuola.net/news_foto/image-grabber/image-5bfb139eb1b4d.jpg",200,"marco@gmail.com","Prima Guerra",@nuovo_id);
CALL Inserisci_DOMANDA_APERTA_PREMIUM("Qual'è la tua squadra preferita?",1,"https://play-lh.googleusercontent.com/zhZWsqHQVQJf_pRimq97B24NQq8zPv_E4aW4l-RJlU5s_cDSUFo41da-D-dlJqU1tqI",100,"marco@gmail.com","Champions League",@nuovo_id);
CALL Inserisci_RISPOSTA_APERTA("121","Cagliari","rossi@gmail.com",@nuovo_id,100);
CALL Inserisci_DOMANDA_APERTA_PREMIUM("Chi ha vinto più champions?",1,"https://static.vecteezy.com/system/resources/previews/010/994/307/original/real-madrid-logo-symbol-black-and-white-design-spain-football-european-countries-football-teams-illustration-free-vector.jpg",100,"marco@gmail.com","Champions League",@nuovo_id);
CALL Inserisci_DOMANDA_APERTA_AZIENDA("Le elezioni sono state truccate?",1,"https://www.repstatic.it/content/nazionale/img/2020/08/26/075249306-2b49128f-fa7e-4b4f-a81a-ec226b03b282.jpg",100,"ssss","Elezioni",@nuovo_id);
CALL Inserisci_DOMANDA_APERTA_AZIENDA("Quale attore pensi che possa vincere un oscar?",1,"https://static.sky.it/editorialimages/39313a43835a6bfe3fed74601d42dda082df44c9/skytg24/it/spettacolo/cinema/2020/10/29/attori-single/00_brad-pittl_getty.jpg",100,"ssss","Attori",@nuovo_id);
CALL Inserisci_RISPOSTA_APERTA("122","Brad Pitt","maurizio@gmail.com",@nuovo_id,100);
CALL Inserisci_DOMANDA_APERTA_AZIENDA("Film con più oscar?",1,"https://www.mboario.com/WebRoot/ce_it/Shops/990428805/59BD/4089/CDC9/EC69/89E7/C0A8/190E/F86E/buy-oscar-statue-0db88f.jpg",100,"ssss","Attori",@nuovo_id);
CALL Inserisci_DOMANDA_CHIUSA_PREMIUM("Chi andrà in semi-finale?",1,"https://play-lh.googleusercontent.com/zhZWsqHQVQJf_pRimq97B24NQq8zPv_E4aW4l-RJlU5s_cDSUFo41da-D-dlJqU1tqI","marco@gmail.com","Champions League",@nuovo_id);
CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(@nuovo_id,1,"milan");
CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(@nuovo_id,2,"napoli");
CALL Inserisci_RISPOSTA_CHIUSA("123","napoli","rossi@gmail.com",@nuovo_id);
CALL Inserisci_DOMANDA_CHIUSA_PREMIUM("Chi andrà in semi-finale?",1,"https://play-lh.googleusercontent.com/zhZWsqHQVQJf_pRimq97B24NQq8zPv_E4aW4l-RJlU5s_cDSUFo41da-D-dlJqU1tqI","marco@gmail.com","Champions League",@nuovo_id);
CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(@nuovo_id,1,"Real Madrid");
CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(@nuovo_id,2,"Chelsea");
CALL Inserisci_DOMANDA_CHIUSA_AZIENDA("Quale tra questi è l'attore più importante?",1,"https://static.sky.it/editorialimages/39313a43835a6bfe3fed74601d42dda082df44c9/skytg24/it/spettacolo/cinema/2020/10/29/attori-single/00_brad-pittl_getty.jpg","ssss","Attori",@nuovo_id);
CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(@nuovo_id,1,"primo");
CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(@nuovo_id,2,"secondo");
CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(@nuovo_id,3,"terzo");
CALL Inserisci_RISPOSTA_CHIUSA("124","primo","maurizio@gmail.com",@nuovo_id);
CALL Inserisci_DOMANDA_CHIUSA_AZIENDA("Quanti anni ha tom holland?",1,"https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSRYQ0jSDlCnykK0eUGCatSbbXFykVCGyRZ4w&usqp=CAU","ssss","Attori",@nuovo_id);
CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(@nuovo_id,1,"25");
CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(@nuovo_id,2,"26");
CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(@nuovo_id,3,"27");
CALL Inserisci_DOMANDA_CHIUSA_PREMIUM("Quando fini la prima guerra mondiale?",1,"http://www.novecento.org/wp-content/uploads/2014/10/French_soldiers_ditch_1914.jpg","marco@gmail.com","Prima Guerra",@nuovo_id);
CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(@nuovo_id,1,"1918");
CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(@nuovo_id,2,"1919");
CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(@nuovo_id,3,"1920");
CALL Inserisci_DOMANDA_CHIUSA_AZIENDA("Chi è il presidente della repubblica",1,"https://www.africarivista.it/wp-content/uploads/2022/07/mattarella.jpg","ssss","Elezioni",@nuovo_id);
CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(@nuovo_id,1,"Sergio Mattarella");
CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(@nuovo_id,2,"Giorgio Napolitano");
CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(@nuovo_id,3,"Giorgia Meloni");
CALL Inserisci_DOMANDA_APERTA_AZIENDA("Chi ha vinto l'Europa League nella stagione 2021-2022 ",1,"https://upload.wikimedia.org/wikipedia/it/2/21/UEFA_Europa_League_logo_%282021%29.svg",100,"ssss","Europa League",@nuovo_id);
CALL Inserisci_DOMANDA_CHIUSA_PREMIUM("Quale film ha incassato di più negli ultimi 10 anni?",1,"https://il-cubo.it/images/cover19.jpg","marco@gmail.com","Ultimi Film",@nuovo_id);
CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(@nuovo_id,1,"Avatar");
CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(@nuovo_id,2,"Avengers End Game");
CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(@nuovo_id,3,"Fast and Furious 7");
CALL Inserisci_DOMANDA_CHIUSA_PREMIUM("In che anno è uscito titanic?",1,"https://www.spettegolando.it/wp-content/uploads/2021/03/Canale.jpg","marco@gmail.com","Film Vecchi",@nuovo_id);
CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(@nuovo_id,1,"1996");
CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(@nuovo_id,2,"1997");
CALL Inserisci_DOMANDA_CHIUSA_OPZIONE(@nuovo_id,3,"1998");
CALL Inserisci_INTERESSAMENTO("rossi@gmail.com","Generale");
CALL Inserisci_INTERESSAMENTO("rossi@gmail.com","Sport");
CALL Inserisci_INTERESSAMENTO("maurizio@gmail.com","Cinema");
CALL Inserisci_INTERESSAMENTO("maurizio@gmail.com","Sport");
CALL Inserisci_PREMI_DISPONIBILI("1","rontinim@gmail.com","Principiante","https://thumbs.dreamstime.com/b/beginner-stamp-grunge-vintage-isolated-white-background-sign-148425622.jpg","Principiante dei Sondaggi",2);
CALL Inserisci_PREMI_DISPONIBILI("2","rontinim@gmail.com","Intermedio","https://www.lingobest.com/free-online-english-course/wp-content/uploads/2022/08/Intermediate.png","Intermedio dei Sondaggi",5);
CALL Inserisci_PREMI_DISPONIBILI("3","rontinim@gmail.com","Maestro","https://upload.wikimedia.org/wikipedia/commons/thumb/8/80/Maestro_2016.svg/300px-Maestro_2016.svg.png","Maestro dei Sondaggi",10);
CALL Inserisci_PREMI_DISPONIBILI("12","rontinim@gmail.com","Sondaggio D'ORO","https://th.bing.com/th/id/OIP.NelNbbze3Wk44CZsuAB1pQHaEo?pid=ImgDet&rs=1","Per il miglior sondaggio della categoria",0);
CALL Inserisci_VINCITA_PREMI("rossi@gmail.com","1"); 
CALL Inserisci_VINCITA_PREMI("rontinim@gmail.com","12");
CALL Inserisci_INVITO_PREMIUM("4","Attesa","rontinim@gmail.com","marco@gmail.com","Prima Guerra");
CALL Inserisci_INVITO_PREMIUM("6","Attesa","rontinim@gmail.com","marco@gmail.com","Ultimi Film");
CALL Inserisci_INVITO_PREMIUM("9","accepted","rossi@gmail.com","marco@gmail.com","Champions League");
CALL Inserisci_INVITO_AZIENDA("5","reject","maurizio@gmail.com","ssss","Elezioni");
CALL Inserisci_INVITO_AZIENDA("1","Attesa","maurizio@gmail.com","ssss","Attori");
CALL Inserisci_INVITO_AZIENDA("20","reject","maurizio@gmail.com","ssss","Europa League");