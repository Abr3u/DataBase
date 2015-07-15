<html>
<body>
<?php
	// inicia sessão para passar variaveis entre ficheiros php
	session_start();
	$username = $_SESSION['username'];

	// Função para limpar os dados de entrada
		function test_input($data) {
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data);
		return $data;}

echo("Obrigado pela sua visita, $username<br>");
echo("Carregue no botao para voltar a iniciar sessao<br><br>");
//termina a sessão
session_destroy();

echo("<a href='login.htm'><button>voltar para login</button></a>");
?>

