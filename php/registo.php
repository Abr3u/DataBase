<html>
	<body>
		<?php
		// inicia sessão para passar variaveis entre ficheiros php
		session_start();
		// Função para limpar os dados de entrada
		function test_input($data) {
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data);
			return $data;
		}

		// Carregamento das variáveis username e pin do form HTML através do metodo POST;
		if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"]) && isset($_POST["pin"])) {
			$_SESSION['username'] = test_input($_POST["username"]);
			$_SESSION['pin'] = test_input($_POST["pin"]);
		}
		 
		 // passa variaveis para a sessao;
		$username = $_SESSION['username'];
		$pin = $_SESSION['pin'];
		
		//Variáveis de conexão à BD
		$host = "db.ist.utl.pt";
		// o MySQL esta disponivel nesta maquina
		$user = "ist176370";
		//-> substituir pelo nome de utilizador
		$password = "lldx0788";
		//-> substituir pela password dada pelo mysql_reset
		$dbname = $user;
		// a BD tem nome identico ao utilizador

		echo("<p>Projeto Base de Dados Parte II</p><br>");
		
		$connection = new PDO("mysql:host=" . $host . ";dbname=" . $dbname, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

		//echo("<p>Connected to MySQL database $dbname on $host as user $user</p>\n");

		// obtem o pin da tabela pessoa
		$sql = "SELECT * FROM pessoa WHERE nif=" . $username;
		$result = $connection -> query($sql);

		if (!$result) {
			echo("<p> Erro na Query:($sql)<p>");
			exit();
		}

		foreach ($result as $row) {
			$safepin = $row["pin"];
			$nif = $row["nif"];
		}
		if ($safepin != $pin) {
			echo "<p>Pin Invalido! Exit!</p>\n";
			$connection = null;
			exit ;
		}
		//echo "<p>Pin Valido!</p>\n";
		
		?>
		<html><form action='logout.php' method='POST'>
		<input type='submit' value='LOGOUT'>
		</form></html>
		<?php
		
		// Apresenta os leilões - versao anterior
		//$sql = "SELECT lr.lid,l.dia,l.nif,l.nome,l.valorbase, l.nrleilaonodia  FROM leilao l JOIN leilaor lr where l.nif=lr.nif and l.dia=lr.dia and l.nrleilaonodia=lr.nrleilaonodia and lr.dia <= date(now()) and date(now()) <= lr.dia + interval lr.nrdias day group by lid";
		
		?>
		<html><h3>Leiloes em curso ou a iniciar</h3></html>
		<?php
		
		$sql = "SELECT lr.lid,l.dia,l.nif,l.nome,l.valorbase, l.nrleilaonodia  FROM leilao l JOIN leilaor lr where l.nif=lr.nif and l.dia=lr.dia and l.nrleilaonodia=lr.nrleilaonodia and date(now()) <= lr.dia + interval lr.nrdias day group by lid";
		$result = $connection -> query($sql);
		echo("<table border=\"1\" \n");
		echo("<tr><td>lid</td><td>nif</td><td>dia</td><td>NrDoDia</td><td>nome</td><td>valorbase</td></tr>\n");
		$idleilao = 0;
		foreach ($result as $row) {
			$idleilao = $idleilao + 1;
			echo("<tr><td>");
			echo($row["lid"]);
			echo("</td><td>");
			echo($row["nif"]);
			echo("</td><td>");
			echo($row["dia"]);
			echo("</td><td>");
			echo($row["nrleilaonodia"]);
			echo("</td><td>");
			echo($row["nome"]);
			echo("</td><td>");
			echo($row["valorbase"]);
			echo("</td><td>");
		}
		echo("</table>\n<br><br>");
		
		?>
		<html><h3>Lance maximo de cada Leilao em que esta inscrito</h3></html>
		<?php

		$sql = "select v.lid,v.Maximo, v.diasAteAoFinal
from (select leilao as lid from concorrente where pessoa = " . $username. ") as tab 
inner join MyView v on v.lid = tab.lid";
		
		$result = $connection -> query($sql);
		
		echo("<table border=\"1\">\n");
		echo("<tr><td>lid</td><td>Maximo</td><td>diasAteAoFinal</td></tr>\n");
		foreach ($result as $row) {
			echo("<tr><td>");
			echo($row["lid"]);
			echo("</td><td>");
			echo($row["Maximo"]);
			echo("</td><td>");
			echo($row["diasAteAoFinal"]);
			echo("</td><td>");			
		}
		echo("</table>\n");
		?>

		<form action="leilao.php" method="post">
			<h3>Escolha o ID do leilao que pretende concorrer</h3>
			<p>
				ID :
				<input type="text" name="lid" />
			</p>
			<p>
				<input type="submit" />
			</p>
		</form>
		
		<form action="lance.php" method="post">
			<h3>Insira o ID do leilão e o valor do respectivo lance</h3>
			<p>
				ID:
				<input type="text" name="lid" />
			</p>
			<p>
				Lance:
				<input type="text" name="valor" />
			</p>
			<p>
				<input type="submit" />
			</p>
		</form>		

<form action="leilaoTransact.php" method="post">
	<h3>Escolha o ID do leilão que pretende concorrer(inscrição atomica) <br> Inserir os lids dos leiloes pretendidos separados por virgulas EX: 1,2,5,6,7 </h2>
	<p>
		ID :
		<input type="text" name="lids" />
	</p>
	<p>
		<input type="submit" />
	</p>
</form>		
</body>
</html>
