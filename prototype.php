<?php 
$parameter_name = array('Nome'=>'[^A-z,0-9,_]','Marca'=>'[^A-z,0-9,_]','Prezzo_Vendita'=>'[^0-9]','Prezzo_Acquisto'=>'[^0-9]','Iva'=>'[^0-9]'); //array di riferimento dei parametri del database @todo spostarlo in php config
//creo una connessione al database
//require_once('config.php');
$con= mysqli_connect('localhost','root','root','DB_Pweb');
$QueryType = $_POST['query'];
//$query = AddParams($QueryType);
//echo $query;
SendResponse($QueryType , $con);
//SimpleSendResponse($con);
//chiudo la connessione al DB
mysqli_close($con);



//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//funzione semplice di prova

function SimpleSendResponse($con,$query)
{
	$result = mysqli_query($con , $query);
	//stampa dei risultati
	while ($row = mysqli_fetch_array($result)) 
	{
		echo '<tr>';
		echo ('<td>'.$row['Id'].'</td>'.'<td>'.$row['Nome'].'</td>');
		echo '</tr>';
	}
}

//funzione "ufficiale"

function SendResponse($QueryType , $con)
{
	if($QueryType == 3)
	{
		$query = AddParams( $QueryType);
		mysqli_query($con,$query);
		//aggiorno i record visualizzati chiamando nuovamente la funzione con querytype = 1
		$QueryType = 1;
		SendResponse($QueryType);
	}
	else
	{
		
	 	$query = AddParams($QueryType);	
	 	$result = mysqli_query($con,$query);
		$log_prova = $query;
	 	PrintResult($result,$log_prova);

		//funzione di test
	//	SimpleSendResponse($con,$query);
	}
	
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

//funzione per il ritorno dei valori
//genera la risposta in formato xml 
function PrintResult($result,$log)
{ 	
	//nuova funzione adattata all'utilizzo di xml 
	header( "content-type: application/xml; charset=UTF-8" );	
	$xmlDoc= new DOMDocument('1.0','UTF-8');
	$xmlDoc->formatOutput = true;
	$xmlRoot = $xmlDoc->createElement('xml');
	$xmlRoot= $xmlDoc->appendChild($xmlRoot);
	$xmlResult = $xmlDoc->createElement('result');
	$xmlRoot->appendChild($xmlResult);
	while($row = mysqli_fetch_array($result))
	{	
		$e ='<tr>'.'<td>'.$row['Id'].'</td>'.'<td>'.$row['Nome'].'</td>'.'</tr>';
		$p = $xmlDoc->createTextNode($e);
		$xmlResult->appendChild($p);
	}

	$xmlLog = $xmlDoc->createElement('log',$log);
	$xmlRoot->appendChild($xmlLog);

	//stampa del file xml
	echo $xmlDoc->saveXML();

}


//funzione per il controllo dei valori (@TODO deve generare un errore riconoscibile dal client nella funzione che gestisce gli errori )
function CheckParams($parameter_name)
{ 
	foreach ($parameter as $x=>$x_value)
	{

	}
	
}

//funzione che crea la query a partira dai dati inviati al server
function AddParams($QueryType)
{
	//global $parameter_name; ----->non funziona 
	$parameter_name = array('Nome','Marca','Prezzo_Vendita','Prezzo_Acquisto','Iva');
	//carico il database
	if($QueryType ==1)
	{
		$query_1 = 'SELECT * FROM Prodotti';
		return $query_1;
	}
	
	//cerco un valore nel database
	if($QueryType == 2)
	{
	$query_2 = 'SELECT * FROM Prodotti WHERE ';
	foreach($parameter_name as $value)
	{
		if($_POST[$value]!= NULL ||$_POST[$value]!= '') //---->devo controllare che contenga almeno un carattere
		{
			$query_2.=$value.'='.'"'.$_POST[$value].'"'.' ';
		}		
	}
	return $query_2;
	}
	
	//da testare
	//aggiungo un nuovo record
	if($QueryType==3)
	{
		$query_3 = 'INSERT INTO Prodotti VALUES(';
		//************************************
		foreach($parameter_name as $value)
		{
			if($_POST[$value])
			{
				$query_3.=$_POST[$value];//se non funziona è perchè mancano le virgolette ai valori 
			}
			else
			{
				$query_3.='NULL';
			}
			if($value != $parameter_name[count($parameter_name)-1])
			{
				$query_3.=',';
			}	
		}
		$query_3 .=')';
		return $query_3;	
	}
}
 
?>
