<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html lang='pt-BR'>
<head>
<title>Digital Shield Security</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="shortcut icon" href="../styles/images/favicon.ico">
<link rel="stylesheet" type="text/css" href="/styles/global.css">

</head>

<body>
<?php
  session_start();
  $tabela=$_POST["tabela"];
  $campos_notnull = $_POST["array"];
  print "<h2 style='padding-left:1%'>INSERIR REGISTRO NA TABELA: " .$tabela. " </h2>";

  include ("../conexaoBD.php");
  require_once ($_SERVER['DOCUMENT_ROOT']."/apis/audit/AuditMgr.php");

  //obtem os nomes dos campos da tabela (camada) da vari�vel $cama
  if (strcmp($tabela,"unidades")==0) $sql = "select * from " .$tabela;
  else $sql = "select * from administrador." .$tabela;
  $res = pg_query($dbcon, $sql);

  if (strcmp($tabela,"unidades")==0)
    $sql = "insert into " .$tabela. " ("; 
  else
    $sql = "insert into administrador." .$tabela. " (";

  $sqlValor = " values (";
  $nome_coluna_pk = pg_field_name($res, 0); 
  $sql2 = "select max(".$nome_coluna_pk.") from administrador." . $tabela;
  $res2 = pg_query($dbcon, $sql2);
  $idFetch = pg_fetch_row($res2);
  $sql .= $nome_coluna_pk.", ";
  $id = $idFetch[0] == '' ? '1' : $idFetch[0] + 1;
  $sqlValor .= $id . ", ";

  //define o sql para inserir na tabela espec�fica os dados do novo registro
  $i = pg_num_fields($res);
  for ($j = 1; $j < $i; $j++) { 
    $post_valor= $_POST[pg_field_name($res, $j)];
    if ((strcmp($tabela,"permissao")==0) && ($post_valor==""))
      $post_valor="FALSE";
    elseif ($post_valor == '' and in_array(pg_field_name($res, $j), $campos_notnull)) { # se for campo NOT NULL
      header("Location: FormInserirRegistro.php?tabela=".$tabela."&empty=true"); #retorna com erro
      pg_close($dbcon); 
      exit;
    }
    elseif (strcmp(pg_field_name($res, $j), "senha") == 0) {
      $post_valor = sha1($_POST[pg_field_name($res, $j)]);
    }
    if (strcmp($tabela, "perbotoes") == 0 and empty($post_valor)) {
      $post_valor = null;
    }
    if ($j!=($i-1)) {
      if (empty($post_valor)) {
        $sql = $sql .pg_field_name($res, $j). ", ";
        $sqlValor = $sqlValor ."null, ";
      } else {
        $sql = $sql .pg_field_name($res, $j). ", ";
        $sqlValor = $sqlValor . "'" .$post_valor. "', ";
      }
    } else {
      if (empty($post_valor)) {
        $sql = $sql .pg_field_name($res, $j). ") ";
        $sqlValor = $sqlValor . "null)";
      } else {
        $sql = $sql .pg_field_name($res, $j). ") ";
        $sqlValor = $sqlValor . "'" .$post_valor. "')";
      }
    }  
  }

  //Consulta sql com o registro a ser inserido na tabela
  $sql= $sql . $sqlValor;
  AuditMgr::createAudit($_SESSION["nome"], "CREATE", $sql);
  echo $sql;

  // insere os dados na tabela se estiverem todos adequados e consistidos
  $res = pg_query($dbcon, $sql);
  if (!$res) {
    echo "<br> Erro na atualização dos dados.";
    exit;
  }

  print "<script>alert('Registro salvo com sucesso.'); window.close();</script>";
  pg_close($dbcon); 
?>
</body>
</html>
