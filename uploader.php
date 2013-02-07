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
	$dsn = 'mysql:host=localhost; dbname=hgf; port=3306';
	$user = 'hgf';
	$password = 'bioinfo2012';
	$options = array(
		PDO::ATTR_PERSISTENT => true,
		PDO::ATTR_CASE => PDO::CASE_LOWER
	);
	$target = "./uploads/"; 
	$target = $target . basename( $_FILES['uploaded']['name']);  
	$ok=1; 
	if(eregi(".fasta$", $_FILES['uploaded']['name']) or eregi(".fas$", $_FILES['uploaded']['name'])){
		if(move_uploaded_file($_FILES['uploaded']['tmp_name'], $target)) 
		{
			//abre o banco de dados testdb          
			try {
				$db = new PDO($dsn, $user, $password, $options);
				//insere registros em task
				$file = $_FILES['uploaded']['name'];	
				$user = $_SESSION['navegacao'];
				$sql = "INSERT INTO hgf.task (filein, status, user_id, start_date) VALUES ('$file', 'uploaded', '$user', now())";
				$execute = $db->exec($sql);
				//fecha a "conexão" com o banco de dados
				$db = null;
			}//try
			catch (PDOException $e) {
				echo 'Erro: '.$e->getMessage();
			}//catch
		} 
		else {
			echo "Sorry, there was a problem uploading your file.";
		}
	}
	else {
		echo "Invalid file extension!";
	}
	header("Location: upinterfaceweb.php");
?>   
