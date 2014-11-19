<?php 

//creo una connessione al database
require_once('config.php');
$con= mysqli_connect(HOST,USR,PSWD,DB);
$QueryType = $_POST['query'];
SendResponse($QueryType , $con);

//chiudo la connessione al DB
mysqli_close($con);




function SendResponse($QueryType , $con ,$message='empty' )
{
	if($QueryType == 3)
	{
		$query = AddParams( $QueryType);
		mysqli_query($con,$query);
		$log = $query;
		//aggiorno i record visualizzati chiamando nuovamente la funzione con querytype = 1
		$QueryType = 1;
		SendResponse($QueryType , $con,$log);
	}
	else
	{
		
	 	$query = AddParams($QueryType);	
	 	$result = mysqli_query($con,$query);
		$log;	
		if($message == 'empty')
		{
			$log=$query;
		}
		else
		{
			$log=$message;
		}
	 	PrintResult($result,$log);
	}
	
}



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
	$int = '<th>Id</th><th>Nome</th><th>Marca</th><th>Prezzo Vendita</th><th>Prezzo Acquisto</th><th>Iva</th>'; 
	$pint = $xmlDoc->createTextNode($int);
	$xmlResult->appendChild($pint);
	while($row = mysqli_fetch_array($result))
	{	
		$e ='<tr>'.'<td>'.$row['Id'].'</td>'.'<td>'.$row['Nome'].'</td>'.'<td>'.$row['Marca'].'</td>'.'<td>'.$row['Prezzo_Vendita'].'</td>'.'<td>'.$row['Prezzo_Acquisto'].'</td>'.'<td>'.$row['Iva'].'</td>'.'</tr>';
		$p = $xmlDoc->createTextNode($e);
		$xmlResult->appendChild($p);
	}

	$xmlLog = $xmlDoc->createElement('log',$log);
	$xmlRoot->appendChild($xmlLog);

	//stampa del file xml
	echo $xmlDoc->saveXML();

}



//funzione che crea la query a partira dai dati inviati al server
function AddParams($QueryType)
{
	//global $parameter_name; ----->non funziona 
	$parameter_name = array('Nome','Marca','Prezzo_Vendita','Prezzo_Acquisto','Iva');
	//carico il database
	if($QueryType ==1)
	{
		$query_1 = 'SELECT * FROM '.TB;
		return $query_1;
	}
	
	//cerco un valore nel database
	if($QueryType == 2)
	{
	$query_2 = 'SELECT * FROM '.TB.' WHERE ';
	foreach($parameter_name as $value)
	{
		if($_POST[$value]!= NULL ||$_POST[$value]!= '') //---->devo controllare che contenga almeno un carattere
		{
			$query_2.=$value.'='.'"'.$_POST[$value].'"'.' ';
		}		
	}
	return $query_2;
	}
	
	
	//aggiungo un nuovo record
	if($QueryType==3)
	{
		$query_3 = 'INSERT INTO '.TB.' VALUES(DEFAULT,';
	
		foreach($parameter_name as $value)
		{
			if($_POST[$value])
			{
				$query_3.='"'.$_POST[$value].'"';
			}
			else
			{
				$query_3.='"'.'NULL'.'"';
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