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
  $tabela=$_GET["tabela"];
  $id=$_GET["id"];
  $campo_id= $_GET["campo_id"];
  print "<h2 style='padding-left:1%'>DADOS DO REGISTRO DA TABELA: " .$tabela. " </h2>";

  include ("../conexaoBD.php");
  require_once ($_SERVER['DOCUMENT_ROOT']."/apis/audit/AuditMgr.php");

  //obtem a consulta sql para deletar o registro
  if (strcmp($tabela,"unidades")==0) $sql = "delete from " .$tabela. " where ".$campo_id."='" .$id."' ";
  else $sql = "delete from administrador." .$tabela. " where ".$campo_id."='" .$id."' ";

  AuditMgr::createAudit($_SESSION["nome"], "DELETE", $sql);

  $res = pg_query($dbcon, $sql);

  if (!$res) {
    echo "<br> Erro na atualização dos dados.";
    exit;
  }
  print "<script>alert('Registro deletado com sucesso.'); window.close();</script>";
  pg_close($dbcon); 
?>
</body>
</html>
