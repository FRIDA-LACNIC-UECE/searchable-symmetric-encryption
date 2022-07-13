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
  if(!isset($_SERVER['HTTP_REFERER'])){
    // redirect them to your desired location
    header('Location: ../restricted.php');
    exit;
  }
  session_start();
  error_reporting(0);
  $tabela=$_GET["tabela"];
  $id=$_GET["id"];
  $campo_id= $_GET["campo_id"];
  print "<h2 style='padding-left:1%'>DADOS DO REGISTRO DA TABELA: " .$tabela. " </h2>";

  include ("conexaoBD.php");
  require_once ($_SERVER['DOCUMENT_ROOT']."/apis/audit/AuditMgr.php");

  //obtem a consulta sql que ir� deletar o registro
  $sql = "delete from " .$tabela. " where ".$campo_id."='".$id."'";
  AuditMgr::createAudit($_SESSION["nome"], "DELETE", $sql);

  $res = pg_query($dbcon, $sql);

  if ($res) {
    print "<script>alert('Registro deletado com sucesso.'); window.close();</script>";
    pg_close($dbcon); 
  }
  else {
    echo "<h2>Erro na deleção dos dados</h2>";
    //Consulta sql com o registro a ser inserido na tabela
    print "SQL:<br>".$sql."<br>";
    $error = pg_last_error($dbcon);
    echo "<br><span style='color: red'>$error</span>";
    exit;
  }
?>
</body>
</html>
