
<html>
<body>
<?php
	// inicia sessão para passar variaveis entre ficheiros php
	session_start();
	$nif = $_SESSION['username'];

	// Função para limpar os dados de entrada
		function test_input($data) {
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data);
		return $data;}

// Carregamento das variáveis username e pin do form HTML através do metodo POST;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
$valor = test_input($_POST["valor"]);
$lid = test_input($_POST["lid"]);}

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
// verifica se a pessoa esta inscrita no respectivo leilao
$sql = "SELECT * FROM concorrente WHERE pessoa=" . $nif ." AND leilao=". $lid;
$result = $connection -> query($sql);


$sql = "SELECT COUNT(*) AS id FROM concorrente WHERE pessoa=" . $nif ." AND leilao=". $lid;
$result = $connection -> query($sql);

foreach($result as $row){
	$count = $row["id"];
}

// condicao de seguranca
if($count == 0){
	echo("<p> Pessoa nao registada no leilao <p>");
	exit();
}

// verifica se o leilão ainda está a decorrer
$sql = "SELECT COUNT(*) AS id FROM leilaor WHERE lid =" . $lid. " AND dia + interval nrdias day > DATE(now())";
$result = $connection -> query($sql);

foreach ($result as $row){$count = $row["id"];}

if($count == 0){
	echo("<p> Leilao ja terminou <p>");
	exit();
}

$sql = "INSERT INTO lance (pessoa,leilao,valor) VALUES ($nif,$lid,$valor)";
$result = $connection->query($sql);

if (!$result) {echo("<p> Lance nao registado <p>");
exit();}
echo("<p> Lance de valor ($valor) registado com sucesso no leilao ($lid)</p>\n");
?>
</body>
</html>
