<!DOCTYPE html>
<html>
	<head>
		<title>Bioinformática UFPR: HGF gene marking tool</title>
		<meta name="Content-type" content="text/html"; charset= utf-8 />
		<link rel="stylesheet"type="text/css"href="style.css" />
		<script language="javascript" type="text/JavaScript" src="jquery.js"></script>
		<!--meta http-equiv="refresh" content="5;url=http://localhost/upinterfaceweb.php"-->
		<script type="text/javascript">

			  var _gaq = _gaq || [];
			  _gaq.push(['_setAccount', 'UA-34743812-1']);
			  _gaq.push(['_trackPageview']);

			  (function() {
			    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			  })();

		</script>
		
		<script type="text/JavaScript">
                <!--
                redirectTime = "1500";
                redirectURL = "./upinterfaceweb.php";
                function timedRedirect() {
                        setTimeout("location.href = redirectURL;",redirectTime);
                }
                //      -->
                </script>
	</head>
	<body>
		<div id="wrapper">
			<!--START OF HEADER-->
			<div id="header">
				<h1 class="headermain">HGF gene marking tool</h1>
				<span class="headersecond">Hybrid Gene Finder</span>
			</div>
			 <div class="navbar clearfix">
				<div class="breadcrumb">
				 <ul>
				  <li class="first"><a href="http://www.bioinfo.ufpr.br/">bioinformática</a></li>
				  <li> <span class="accesshide " >&nbsp;</span><span class="arrow sep">&#x25BA;</span> HGF gene marking tool</li>
				 </ul>
				</div>
				<div class="navbutton">&nbsp;</div>
			 </div>
			<!--END OF HEADER-->
			<div id="input">		
				<table class="instr">
				 <tr>
				  <th><a href="./upinterfaceweb.php">HGF</a></th>
				  <th><a href="./instructions.html">Instructions</a></th>
				  <th><a href="./output.html">Output</a></th>
				 </tr>
				</table>
				<div id="forminput">
					<div id="fieldset">		
						<p><b class="legend">Query Sequence</b></p>			
						<b>Enter FASTA Sequence (.fasta) (.fas)</b>
						<form enctype="multipart/form-data" action="uploader.php" method="post"> 
						Please choose a file (max 25M): <input name="uploaded" type="file" /><br />
							<input type="submit" value="Upload File" />
						 </form> 			
						<p><b>Or paste your FASTA sequence(s)</b></p>
						<form method="post" action="uploader2.php">
						<tr>
						 <td>Title (optional):</br></td>
						 <td><input type="text" name="title"></br></td>
						</tr>
						<tr>
						 <td>Sequence text:</br></td>
						 <td><textarea name="sequence" cols="80" rows="5" style="width:80%">
						 </textarea></br></td>
						</tr>
						<input type="submit" value="Submit" />
						</form>
					</div><!--fieldset-->
				</div><!--forminput-->				
				<div id="uploaded">
						<div id="fieldset1">
							<?php
								/* Script para a geração de uma session */
								//iniciando a session, caso não seja ainda
								if (!isset($_SESSION)){
									session_start("data");
									$_SESSION['navegacao']= session_id();
									//pegamos a referência, de onde o usuario veio
									$referencia = $_SERVER["HTTP_REFERER"];	
									$counter= "contador.txt";//abre o arquivo contador
									ignore_user_abort(false);
									if (file_exists($counter)) {
										$counting = file_get_contents(rtrim($counter));    
										$counting++;
									}//if
									else { 
										touch($counter); /* Criamos o ficheiro */
										chmod($counter, 0777); 
										$counting = "1"; // E definimos a primeira visita
									}//else
									$f = @fopen ($counter,"w"); 
									@flock ($f, LOCK_EX); 
									@fwrite($f,$counting); 
									@flock ($f, LOCK_UN); /* Soltamos o ficheiro... */
									@fclose($f); /* e fechamos logo de seguida. */
									ignore_user_abort(true);
								}//if
								//verificamos se existe alguma referencia e se a session está criada
								if(isset($referencia)&& isset($_SESSION['navegacao'])==session_id()){
									//se sim, navega-se novamente
								}//if
								else {
									//caso não, destruimos a sessao
									session_destroy("data");
								}//else
						
								/* Script para listar arquivos do diretório uploads, com os respectivos links */
								$x= 0;
								$dsn = 'mysql:host=localhost; dbname=hgf; port=3306';
								$user = 'hgf';
								$password = 'bioinfo2012';
								$options = array(
									PDO::ATTR_PERSISTENT => true,
									PDO::ATTR_CASE => PDO::CASE_LOWER
								);
								//abre o banco de dados novadb
								try{
									$db = new PDO($dsn, $user, $password, $options);
									$userid = $_SESSION['navegacao'];
									//verifica os arquivos correspondentes ao usuario
									$sql = "SELECT filein FROM task WHERE user_id= ('$userid') AND status= ('uploaded');";
									$check = $db->query($sql);
									$line = $check->fetchALL(PDO::FETCH_ASSOC);
									foreach($line as $row) {
										//recebe o nome do arquivo
										$matrixin[$x]= $row['filein'];
										$x++;
									}//foreach
									//fecha a "conexão" com o banco de dados
									$db = null;
								}//try
								catch (PDOException $e) {
									echo 'Erro: '.$e->getMessage();
								}//catch
								/*if ($handle=opendir("./uploads/")) {//abre diretório para leitura
									$x=0;
									while (false!==($filein=readdir($handle))) {//lê arquivos do diretório
										if ($filein!="." && $filein!="..") {//evita leitura de . e ..
											$matrixin[$x]=$filein;//armazena nomes dos arquivos na matriz
											$x++;
										} //if
									} //while
									closedir($handle);//fecha o diretório
								} //if
								*/
								echo '
									<strong><font size="3" face="Verdana, Arial, Helvetica, sans-serif">'.$x.' 
									uploaded:</font></strong></br>';
								
								$y=0;
								echo '
								<div class="auto"> 
								 <table class="uploads_table">
									<tr>
									 <th><strong><font size="2">Name</font></strong></th>
									 <th><strong><font size="2">Modified Date</font></strong></th>
									</tr>
									';
								while($x> $y) {//imprime links com nomes dos arquivos
									echo '   
										<tr class="upload_table" cellspacing="0">
										 <td class="upload_table" width="80%">
										   <strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="./uploads/'.$matrixin[$y].'" target="_blank">'.$matrixin[$y].'</a></font>
										   </strong></br>
										 </td>
										 <td class="upload_table" width="80%">
										   <strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">'.date("m/d/Y H:i:s",filemtime("./uploads/".$matrixin[$y])).'</font>
										   </strong></br>
										 </td>
										</tr>
										';		 
									$y++;
								}
								echo '</table>
								</div>';
							?>
						</div><!--fieldset1-->
					</div><!--uploaded-->
				<div id="fieldset2">
					<div id="run">	
						<div id="function">
							<?php	
							echo '<a href="jobqueue.php?id='.$_SESSION["navegacao"].'">RUN</a>'; 
							?>
						</div>
						<div id="runsummary">
							Make GBK pre annotation files running HGF
						</div>
					</div>
				</div>	
				<div id="output">
					<div id="results">
						<div id="fieldset2">
							<?php
								/* Script para listar arquivos do diretório results, com os respectivos links */
								//abre o banco de dados novadb
								$y= 0;
								try{
									$db = new PDO($dsn, $user, $password, $options);
									$userid = $_SESSION['navegacao'];
									//verifica os arquivos correspondentes ao usuario
									$sql = "SELECT fileout FROM task WHERE user_id= ('$userid') AND status= ('completed');";
									$check = $db->query($sql);
									$line = $check->fetchALL(PDO::FETCH_ASSOC);
									foreach($line as $row) {
										$matrixout[$y]= $row['fileout'];
										$y++;
									}//foreach
									//fecha a "conexão" com o banco de dados
									$db = null;
								}//try
								catch (PDOException $e) {
									echo 'Erro: '.$e->getMessage();
								}//catch

								/*if ($handle=opendir("./results/")) {//abre diretório para leitura
									$y=0;
									while (false!==($fileout=readdir($handle))) {//lê arquivos do diretório
										if ($fileout!="." && $fileout!="..") {//evita leitura de . e ..
											$matrixout[$y]=$fileout;//armazena nomes dos arquivos na matriz
											$y++;
										} //if
									} //while
									closedir($handle);//fecha o diretório
								} //if
								*/
								echo '<strong><font size="3" face="Verdana, Arial, Helvetica, sans-serif">'.$y.' results:</font></strong></br>';
								$x=0;
								echo '<table class="results_table">
									<tr>
									 <th><strong><font size="2">Name</font></strong></th>
									 <th><strong><font size="2">Size</font></strong></th>
									 <th><strong><font size="2">Modified Date</font></strong></th>
									</tr>';
								while($y> $x) {//imprime links com nomes dos arquivos
									echo '
										<tr class="result_table" cellspacing="0">
										 <td class="result_table" width="50%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
										<a href="./results/'.$matrixout[$x].'" target="_blank">'.$matrixout[$x].'</a></font>
										 </strong></td>
										 <td class="result_table" width="15%">
										   <strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">'.filesize("./results/".$matrixout[$x]).' bytes</font>
										 </strong></td>
										 <td class="result_table" width="20%">
										   <strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">'.date("m/d/Y H:i:s",filemtime("./results/".$matrixout[$x])).'</font>
										 </strong></td>
										 <td class="result_table" width="15%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
										<a href="download.php?file=results/'.$matrixout[$x].'">Download</a>
										</font></br></td>
								';	
									$x++;
								} //while
								echo '</table>';
							?>
						</div><!--fieldset-->
					</div><!--results-->
				</div>
					
				<div id="resetpage">
					<a href="./logout.php">reset page</a>
				</div>	
			</div>
		</div>
	</body>
</html>
