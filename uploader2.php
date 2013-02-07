<?php
	/* Script para a geração de uma session */
	//iniciando a session, caso não seja ainda
	if (!isset($_SESSION)){
		session_start("data");
		$_SESSION['navegacao']= session_id();
		//pegamos a referência, de onde o usuario veio
		$referencia = $_SERVER["HTTP_REFERER"];
	}//if
	//verificamos se existe alguma referencia e se a session está criada
	if(isset($referencia)&& isset($_SESSION['navegacao'])==session_id()){
		//se sim, navega-se novamente
	}//if
	else {
		//caso não, destruimos a sessao
		session_destroy();
	}//else
	//verifica se foi inserido um titulo
	$string = isset($_POST['title']) ? trim($_POST['title']) : '';
	//caso não gera um nome para arquivo de acordo com a data e hora
	if ($string==''){
		$timenow= getdate();
		$string= ($timenow['mday'] . $timenow['month'] .
		$timenow['hours'] . ":" . $timenow['minutes'] . 
		":" . $timenow['seconds'] . ".fasta");
	}
	else {
		$string = $string . ".fasta";	
	}
	$dsn = 'mysql:host=localhost; dbname=hgf; port=3306';
	$user = 'hgf';
	$password = 'bioinfo2012';
	$options = array(
		PDO::ATTR_PERSISTENT => true,
		PDO::ATTR_CASE => PDO::CASE_LOWER
	);
	$target = "uploads/";
	$target = $target . $string;
	//retira espaços do inicio e da final da sequencia
	$sequence = isset($_POST['sequence']) ? trim($_POST['sequence']) : '';
	if ($sequence==''){
		die('Error: sequence not found');
	}//if
	else {
		//cria o arquivo para a sequencia
		$seq= fopen($target, "w+");
		//escreve a sequencia no arquivo
		fwrite($seq, $sequence);
		//fecha o arquivo
		fclose($seq);
		//abre o banco de dados testdb          
		try {
			$db = new PDO($dsn, $user, $password, $options);
			//insere registros em task
			$file = $string;	
			$user = $_SESSION['navegacao'];
			$sql = "INSERT INTO hgf.task (filein, status, user_id) VALUES ('$file', 'uploaded', '$user')";
			$execute = $db->exec($sql);
			//fecha a "conexão" com o banco de dados
			$db = null;
		}//try
		catch (PDOException $e) {
			echo 'Erro: '.$e->getMessage();
		}//catch
	}//else
	header("Location: upinterfaceweb.php");
?>
