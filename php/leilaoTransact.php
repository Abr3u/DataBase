<html>
<body>
<?php
	// inicia sessão para passar variaveis entre ficheiros php
	session_start();
	$username = $_SESSION['username'];
	$nif = $_SESSION['nif'];

	// Função para limpar os dados de entrada
		function test_input($data) {
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data);
		return $data;}

// Carregamento das variáveis username e pin do form HTML através do metodo POST;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
$lids = test_input($_POST["lids"]);}

// Conexão à BD
$host="db.ist.utl.pt";

// o MySQL esta disponivel nesta maquina
$user="ist176370";

//-> substituir pelo nome de utilizador

$password="lldx0788";

//-> substituir pela password dada pelo mysql_reset
$dbname = $user;

// a BD tem nome identico ao utilizador
$connection = new PDO("mysql:host=" . $host. ";dbname=" . $dbname, $user, $password,
array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
//echo("<p>Connected to MySQL database $dbname on $host as user $user</p>\n");

echo("<a href='registo.php'><button>voltar para home</button></a>");

//$lids="1,2,4,3,7";
//$nif = "40";

$nif = $username;
$vecAux= array();
$vecTokens = explode(",", $lids);

		$arrayDatas = array();
		
		$vecTokensAux = $vecTokens;
		
		foreach($vecTokensAux as $valTok){
			$query = "SELECT dia, count(*) as ver FROM leilaor WHERE lid = $valTok and date(now()) <= dia + interval nrdias day";			
			$daysWithAuctions= $connection->query($query);
			
			foreach ($daysWithAuctions as $rowdwa){
				$count = $rowdwa["ver"];
				if ($count == 0) 
				{
					$kont = 0;
					echo("Leilao $valTok ja terminou");
 
					foreach ($vecTokens as $g){
						if($g == $valTok){
							unset($vecTokens[$kont]);
						}
						$kont = $kont +1;
					}
				}
				else{
					$data = $rowdwa['dia'];
					array_push($arrayDatas,$data);
				}
			}
				$arrayDatas = array_unique($arrayDatas);
		}

		foreach($arrayDatas as $row2){
			
			$query = "SELECT lid FROM leilaor WHERE dia='$row2'";
			
			$auctionForDay= $connection->query($query);
		
			$a=array();
		
			foreach($auctionForDay as $aux){
				foreach($vecTokens as $valLids){
					if ($aux['lid']==$valLids){array_push($a,$valLids);}
				}
			}
			
			$result = array_unique($a);
			
			try {
				$connection->beginTransaction();
				
				foreach($result as $val){
					$resultT = $connection->query("INSERT INTO `ist176370`.`concorrente` (`pessoa`, `leilao`) VALUES ($nif, $val)");
			 	    if (!$resultT) throw new PDOException("Transacçao relativa ao dia $row2 falhada - ROLLBACK");
			 	}
			    $connection->commit();
			  	echo("<p>Comit feito da transacao correspondente ao dia $row2</p>\n");
					
			} catch(PDOException $ex) {
			    //Something went wrong rollback!
			    $connection->rollBack();
			    echo $ex->getMessage();
			}
			
			unset($a);
			
		}

$_SESSION['nif'] = $nif;
//$_SESSION['lid'] = $lid;



// to be continued....
//termina a sessão
//session_destroy();


?>

