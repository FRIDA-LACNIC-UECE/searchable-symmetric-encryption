<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html lang='pt-BR'>
<head>
<title>Digital Shield Security</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="shortcut icon" href="../styles/images/favicon.ico">
<link rel="stylesheet" type="text/css" href="../styles/global.css">

</head>

<body>
<?php 
		session_start();
		error_reporting(0);
		$tabela=$_POST["tabela"];
		$campos_notnull = $_POST["array"];
		print "<h2 style='padding-left:1%'>INSERIR REGISTRO NA TABELA: " .$tabela. " </h2>";

		include ("conexaoBD.php");
		require_once ($_SERVER['DOCUMENT_ROOT']."/apis/audit/AuditMgr.php");
      
		//obtem os nomes dos campos da tabela (camada) da variável $cama
		$sql = "select * from " . $tabela;
		$res = pg_query($dbcon, $sql);	  
		$sql = "insert into " .$tabela. " ("; 
		$sqlValor = " values (";

		// pegando o maior id da tabela
		$sql2 = "select max(id) from " . $tabela;
	 	$res2 = pg_query($dbcon, $sql2);
		$idFetch = pg_fetch_row($res2);
		$sql .= "id, ";
	  	$id = $idFetch[0] == '' ? '1' : $idFetch[0] + 1;
		$sqlValor .= $id . ", ";

	  //define o sql para inserir na tabela específica os dados do novo registro
		$i = pg_num_fields($res);
		for ($j = 1; $j < $i; $j++) {
			$field_name = pg_field_name($res, $j);
			$post_valor = $_POST[$field_name];

			if (substr($field_name, 0, 2) == 'fg'){
				if (isset($_POST[$field_name])) {
					$post_valor = 'true'; #checked
				} else {
					$post_valor = 'false'; #unchecked
				}
			}
			elseif ($post_valor == '' and in_array($field_name, $campos_notnull)) { # se for campo NOT NULL
				header("Location: geralFormInserirRegistro.php?tabela=".$tabela."&empty=true"); #retorna com erro
				pg_close($dbcon); 
				exit;
			}
			if ($j!=($i-1)) {
				if (empty($post_valor)){
				  $sql = $sql .pg_field_name($res, $j). ", ";
				  $sqlValor = $sqlValor ."null, ";
				} else {
					if (is_array($post_valor)){
						$aux = 1;
						$sql = $sql .pg_field_name($res, $j). ", ";
						foreach ($post_valor as $item){
							if($aux == 1){
								$sqlValor = $sqlValor . "array[". $item . ",";
							} elseif ($aux < count($post_valor)){
								$sqlValor = $sqlValor . "".$item . ", ";
							} else {
								$sqlValor = $sqlValor . "".$item . "], ";
							}
							$aux = $aux + 1;
						}
					} else {
						$sql = $sql .pg_field_name($res, $j). ", ";
						$sqlValor = $sqlValor . "'" .$post_valor. "', ";
					}
				}
			} else {
				if (empty($post_valor)) {
					$sql = $sql .pg_field_name($res, $j). ") ";
					$sqlValor = $sqlValor . "null)";
				} else {
					if (is_array($post_valor)){
						$aux = 1;
						$sql = $sql .pg_field_name($res, $j). ") ";
						foreach ($post_valor as $item){
							if($aux == 1){
								$sqlValor = $sqlValor . "array[". $item . ",";
							} elseif ($aux < count($post_valor)){
								$sqlValor = $sqlValor . "".$item . ", ";
							} else {
								$sqlValor = $sqlValor . "".$item . "]) ";
							}
							$aux = $aux + 1;
						}
					} else {
						$sql = $sql .pg_field_name($res, $j). ") ";
						$sqlValor = $sqlValor . "'" .$post_valor. "')";
					}
				}
			}
	  } // end for $j

		$sql = $sql . $sqlValor;
		AuditMgr::createAudit($_SESSION["nome"], "CREATE", $sql);

		// insere os dados na tabela se estiverem todos adequados e consistidos
	  $res = pg_query($dbcon, $sql);
	  if ($res) {
			print "<script>alert('Registro salvo com sucesso.'); window.close();</script>";
			pg_close($dbcon); 
		}
		else {
			echo "<h2>Erro na inserção dos dados</h2>";
			//Consulta sql com o registro a ser inserido na tabela
			print "SQL:<br>".$sql."<br>";
			$error = pg_last_error($dbcon);
			echo "<br><span style='color: red'>$error</span>";
			exit;
		}
?>
</body>
</html>
