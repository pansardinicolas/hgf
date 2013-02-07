<html>
        <head>

		<title>Bioinformática UFPR: HGF gene marking tool</title>
                <meta name="Content-type" content="text/html"; charset= utf-8 />
                <link rel="stylesheet"type="text/css"href="style.css" />
		<script type="text/JavaScript">
		<!--
		redirectTime = "1500";
		redirectURL = "./upinterfaceweb.php";
		function timedRedirect() {
			setTimeout("location.href = redirectURL;",redirectTime);
		}
		//	-->
		</script>
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
		<script type="text/javascript">
                <!--
               	/* var ld=(document.all);
		 var ns4=document.layers;
		 var ns6=document.getElementById&&!document.all;
		 var ie4=document.all;
		 if (ns4)
		 	ld=document.carregador_pai;
		 else if (ns6)
		 	ld=document.getElementById("carregador_pai").style;
		 else if (ie4)
		 	ld=document.all.carregador_pai.style;
		 function __loadMostra()
		 {
		 	if(ns4){ld.visibility="hidden";}
		 	else if (ns6||ie4) ld.display="none";
		 }*/
	    	//      -->
                </script>
		<script type="text/javascript">
		<!--	
			function init(){
			 document.getElementById('loading').style.display='none';
			}
		//	-->
		</script>

                <!--meta http-equiv="refresh" content="3;url=http://localhost/upinterfaceweb.php"--> 
        </head>
	<body onload="init();">
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
						 <td><textarea name="sequence" cols="80" rows="5" style="width:100%">
						 </textarea></br></td>
						</tr>
						<input type="submit" value="Submit" />
						</form>

					</div>
				</div>
				<div id="uploaded">
					<div id="fieldset1">
					<?php
						set_time_limit(7200);
						ignore_user_abort(true);
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
							 <th><strong><font size="2">Kind</font></strong></th>
							</tr>
							';
						while($x> $y) {//imprime links com nomes dos arquivos
							echo '   
								<tr class="upload_table" cellspacing="0">
								 <td class="upload_table" width="80%">
								   <strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="./uploads/'.$matrixin[$y].'" target="_blank">'.$matrixin[$y].'</a></font>
								   </strong></br>
								 </td>
								</tr>			 
						';
							$y++;
						}//while 		
						echo '</table>
						</div>';
					?>
					</div>
				</div>
				<!--div id="carregador_pai">
					<div id="carregador_posicao"><div id="carregador">
					<div style="line-height: 30px;" align="center">
					<strong>Aguarde carregando ...</strong>
					</div><div align="center"><img src="ajax-loader.gif" border=0></div>
					</div></div>
					</div>
				</div-->
				<DIV id="loading" style="position:absolute; text-align:center;">
					<div style="line-height:30px;" align="center">
						<strong>Loading ...</strong>
					</div><img src = "ajax-loader.gif" border=0>
				</DIV>
				<div id="output">
					<div id="results">
						<div id="fieldset2">
							<?php
								$dsn = 'mysql:host=localhost; dbname=novadb; port=3306';
								$user = 'root';
								$password = 'paulabioinfo';
								$options = array(
									PDO::ATTR_PERSISTENT => true,
									PDO::ATTR_CASE => PDO::CASE_LOWER
								);
								/* Script para listar arquivos gerados pelo gbk */	
								$y= 0;
								//abre o banco de dados novadb
								try{
									$db = new PDO($dsn, $user, $password, $options);
									$sql = "SELECT id FROM task WHERE user_id= ('$userid') AND status!= ('completed');";
									echo '<table class="results_table">';
									//verifica se os arquivos já estão 'completed'
									while($y < $x){
										$check= $db->query($sql);
										$line = $check->fetch(PDO::FETCH_ASSOC);
										$line= $line['id'];
										$sql2 = "SELECT status FROM task WHERE id= ('$line');";
										$check= $db->query($sql2);
										$line2 = $check->fetch(PDO::FETCH_ASSOC);
										while($line2['status']!="completed"){
										
											//delay para diminuir o acesso ao bd
											ob_flush();
											flush();
											sleep(5);
											$check= $db->query($sql2);
											$line2 = $check->fetch(PDO::FETCH_ASSOC);
										}//while
										$sql3 = "SELECT fileout FROM task WHERE id= ('$line');";	
										$check = $db->query($sql3);
										$line3 = $check->fetch(PDO::FETCH_ASSOC);
										$matrixout[$y]= $line3['fileout'];   
										echo '
										<tr class="result_table" cellspacing="0">
										<td class="result_table" width="80%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
										<a href="./results/'.$matrixout[$y].'" target="_blank">'.$matrixout[$y].'</a></font>
										</strong></td>
										<td class="result_table" width="15%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
										<a href="download.php?file=results/'.$matrixout[$y].'">Download</a>
										</font></strong></br></td>
										';
										$y++;
									}//while
									echo '</table>';
									//fecha a "conexão" com o banco de dados
									$db = null;
								}//try
								catch (PDOException $e) {
									echo 'Erro: '.$e->getMessage();
								}//catch
								//redireciona para a página inicial(upinterfaceweb)
								echo "<script language = 'JavaScript'>timedRedirect(); </script>";
							?>
						</div>
					</div>
				</div>
				<div id="fieldset2">
					<div id="run">
						<div id="function">
						       <p> <a href="runfunction.php">RUN</a></p>
						</div>
						<div id="runsummary">
							Make GBK pre annotation files running HGF
						</div>
					</div>
				</div>
				<div id="resetpage">
					<a href="./logout.php">reset page</a>
				</div>
			</div>
		</div>
	</body>
</html>
