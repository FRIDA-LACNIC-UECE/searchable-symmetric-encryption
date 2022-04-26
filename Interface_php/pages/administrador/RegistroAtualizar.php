<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html lang='pt-BR'>
<head>
<title>Digital Shield Security</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="shortcut icon" href="styles/images/favicon.ico">
<link rel="stylesheet" type="text/css" href="styles/global.css">

</head>

<body>

<?php
	session_start();
  $tabela=$_POST["tabela"];
  print "<h2 style='padding-left:1%'>DADOS DO REGISTRO DA TABELA: " .$tabela. " </h2>";

  include ("../conexaoBD.php");
  require_once ($_SERVER['DOCUMENT_ROOT']."/apis/audit/AuditMgr.php");

  //obtem o nome do primeiro campo e o seu valor na tabela da vari�vel $tabela
  $id= $_POST["id"];
  $campo_id= $_POST["campo_id"];

  if (strcmp($tabela,"unidades")==0) $sql = "select * from " .$tabela. " where ".$campo_id. "=" .$id;
  else $sql = "select * from administrador." .$tabela. " where ".$campo_id. "=" .$id;
  $res = pg_query($dbcon, $sql);

  if (strcmp($tabela,"unidades")==0) $sql = "update " .$tabela. " set ";
  else $sql = "update administrador." .$tabela. " set ";

  //define o sql para atualizar os dados do registro espec�fico
  $i = pg_num_fields($res);
  for ($j = 1; $j < $i; $j++) { 
    $post_valor= $_POST[pg_field_name($res, $j)];
    if ((strcmp($tabela,"permissao")==0) && ($post_valor=="")) $post_valor="FALSE";
    if ((strcmp(pg_field_name($res, $j),"ativo")==0) && ($post_valor=="")) $post_valor="FALSE";
    if ((strcmp(pg_field_name($res, $j),"senha")==0)) $post_valor=sha1($post_valor);
    if ($j!=($i-1))
      $sql = $sql .pg_field_name($res, $j). "='" .$post_valor. "', ";
    else
      $sql = $sql .pg_field_name($res, $j). "='" .$post_valor. "' ";
  }
  $sql= $sql . " where " .$campo_id. "=" .$id;
  AuditMgr::createAudit($_SESSION["nome"], "UPDATE", $sql);

  // atualiza os dados se tiverem consistidos integralmentes
  //echo $sql;
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
