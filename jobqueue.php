<!DOCTYPE html>
<html>
                <head>
                <title>Bioinformática UFPR: HGF gene marking tool</title>
                <meta name="author" content="Roberto Tadeu Raittz" />
                <meta name="description" content="Gene marking tool"/>
                <meta name="keywords" content="bioinformatics, bioinformatica, hgf, ufpr, bioinfo, gene marking tool, gene, biology, orf, computational biology, gbk, fasta"/>
                <meta name="Content-type" content="text/html"; charset= utf-8 />
                <link rel="stylesheet"type="text/css"href="style.css" />
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
                redirectTime = "15000";
                function timedRedirect() {
                        setTimeout("location.reload(true);",redirectTime);
                }
                //      -->
                </script>
	        </head>
        <body onload="timedRedirect();">
                <!--START OF HEADER -->
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
                <!-- END OF HEADER -->
                <div id="input">
                        <table class="instr">
                                       <tr>
                                         <th><a href="./upinterfaceweb.php">HGF</a></th>
                                         <th><a href="./instructions.html">Instructions</a></th>
                                         <th><a href="./output.html">Output</a></th>
                                        </tr>
                        </table>
               		<div id="fieldset2">
				<?php
					session_start();
					$jobid= isset($_GET["id"])?$_GET["id"]:"";
					if($jobid== ""){
						die("Error: Job doesn't exist");	
					}
					echo'<h1>Job ID: '.$jobid.'</h1>';
					/* Script para atualizar status dos arquivos 'uploaded' */
					$dsn = 'mysql:host=localhost; dbname=hgf; port=3306';
					$user = 'hgf';
					$password = 'bioinfo2012';
					$options = array(
						PDO::ATTR_PERSISTENT => true,
						PDO::ATTR_CASE => PDO::CASE_LOWER
					);
					//abre o banco de dados novadb          
					try {
						$db = new PDO($dsn, $user, $password, $options);
						//contagem de uploads(completed, uploaded, executing)
						$sql= "SELECT COUNT('filein') FROM task WHERE user_id= ('$jobid');";
						$check = $db->query($sql);
						//$line = $check->execute();
						$uploads= $check->fetchColumn();
						if(!$uploads)
							die("Error: Job doesn't exist");
						//contagem de uploads(uploaded)
						$sql= "SELECT COUNT('filein') FROM task WHERE user_id= ('$jobid') AND status<> ('completed');";
						$check = $db->query($sql);
						$uploaded= $check->fetchColumn();
						/*if($uploaded){
							$sbm_time= ($_SESSION['sbm']= date("D M d H:i:s Y"));
							list($usec, $sec)= explode(' ', microtime());
							$_SESSION['ptime']= (float) $sec + (float) $usec;
						}
						else{
							$sbm_time= ($_SESSION['sbm']);
						}
						*/
						//atualiza registros em task
						$sql = "UPDATE task SET status=('submitted') WHERE user_id= ('$jobid') AND status= ('uploaded');";
						$execute = $db->exec($sql);
						//verifica status
						$sql = "SELECT COUNT('filein') FROM task WHERE user_id= ('$jobid') AND status<> ('completed');";
						$check = $db->query($sql);
						$status= $check->fetchColumn();
						//caso aja um erro
						$sql= "SELECT COUNT('filein') FROM task WHERE user_id= ('$jobid') AND status= ('error');";
						$check = $db->query($sql);
						$ferro= $check->fetchColumn();
						if($ferro)
							$status= -1;	
						//verifica horario da submissao do ultimo arquivo
						$sql = "SELECT start_date FROM task WHERE id= ( SELECT MAX(id) FROM task WHERE user_id= ('$jobid'));";
						$check = $db->query($sql);
						$sbm_date= $check->fetchColumn();
						//verifica horario de termino do ultimo arquivo
						$sql = "SELECT finish_date FROM task WHERE id = ( SELECT MAX(id) FROM task WHERE user_id= ('$jobid'));";
						$check = $db->query($sql);
						$cpt_date= $check->fetchColumn();
						//fecha a "conexão" com o banco de dados
						$db = null;
					}//try
					catch (PDOException $e) {
						echo 'Erro: '.$e->getMessage();
					}//catch
				?>
				<hr></hr>
				<p>HGF is running, wait while the results are generated.</br>
				You can go back and keep uploading, then it is possible
				 to come back by pasting your Job ID on the url.
				</p>
				<p>
					 <strong>Link of predicted genes: <b id="link">
				<?php	
					if(($status!= 0)&&($status!= -1))
						echo'Running</b>';
					else
						echo'<a href="./jobid.php?id='.$jobid.'">Output</a></b>';
				?>
					</strong>
				</p>
				<?php
				
				echo'<table class="infos_table">
					<tr class="info_table" cellspacing="0">
					 <td class="info_table" width="50%">
					 Uploads	 
					 </td>
					 <td class="info_table" width="50%">
					  '.$uploads.'
					 </td>
					</tr>
					<tr class="info_table" cellspacing="0">
					 <td class="info_table" width="50%">
					 Files running	 
					 </td>
					 <td class="info_table" width="50%">
					  '.$uploaded.'
					 </td>
					</tr>

					<tr class="info_table" cellspacing="0">
					 <td class="info_table" width="50%">
					 Status	 
					 </td>
					 <td class="info_table" width="50%">';
					if(($status!= 0)&&($status!= -1))
						echo'Running';
					else if($status== 0)
						echo'Completed';
					else
						echo'Error';	
					 echo '</td>
					</tr>
					<tr class="info_table" cellspacing="0">
					 <td class="info_table" width="50%">
					 Submitted date	 
					 </td>
					 <td class="info_table" width="50%">
					'.$sbm_date.'
					</td>
					<tr class="info_table" cellspacing="0">
					 <td class="info_table" width="50%">
					 Completed date	 
					 </td>
					 <td class="info_table" width="50%">
					'.$cpt_date.'
					</td>
					</tr>
				</table>'
				?>
				<strong>This page will be refreshed every 15 seconds.</strong>
			</div>
		 </div>
        </body>
</html>

