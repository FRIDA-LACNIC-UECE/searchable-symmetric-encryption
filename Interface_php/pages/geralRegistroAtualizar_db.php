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
  $tabela=$_POST["tabela"];
  print "<h2 style='padding-left:1%'>DADOS DO REGISTRO DA TABELA: " .$tabela. " </h2>";

  include ("conexaoBD.php");
  require_once ($_SERVER['DOCUMENT_ROOT']."/apis/audit/AuditMgr.php");
    
  //obtem o nome do primeiro campo e o seu valor na tabela da variável $tabela
  $id= $_POST["id"];
  $campo_id= $_POST["campo_id"];
  $campos_notnull = $_POST["array"];
  
  $sql1 = "select * from " .$tabela. " where ".$campo_id. "='" .$id."'";
  $res = pg_query($dbcon, $sql1);    
  $sql = "update " .$tabela. " set ";
  
  //define o sql para atualizar os dados do registro específico
  $i = pg_num_fields($res);

  for ($j = 0; $j < $i; $j++) {
    $field_name = pg_field_name($res, $j);
    $post_valor = $_POST[$field_name];

    // Tipos "flag"
    if (substr($field_name, 0, 2) == 'fg') {
      if (isset($_POST[$field_name])) {
        $post_valor = 'true'; #checked
      } else {
        $post_valor = 'false'; #unchecked
      }
    }
    # se for campo NOT NULL
    elseif ($post_valor == '' and in_array($field_name, $campos_notnull)) {
      header("Location: geralRegistroConsultar_db.php?tabela=".$tabela."&id=".$id."&empty=true"); #retorna com erro
      pg_close($dbcon); 
      exit;
    }

    if ($post_valor == ''):
      continue;
    endif;

    if ($j == 0) {
      if (is_array($post_valor)){
        $aux = 1;
        $total_items = count($post_valor);
        $sql = $sql .pg_field_name($res, $j). " = ";
        foreach ($post_valor as $item){
          if($total_items == 1){
            $sql = $sql . "array['". $item . "'] ";
          }
          elseif($aux == 1){
            $sql = $sql . "array['". $item . "',";
          } elseif ($aux < $total_items){
            $sql = $sql . "'".$item . "', ";
          } else {
            $sql = $sql . "'".$item . "']";
          }
          $aux = $aux + 1;
        }
      } else { 
        $sql = $sql . pg_field_name($res, $j). "='" .$post_valor . "'";
      }
    }
    else {
      if (is_array($post_valor)){
        $aux = 1;
        $total_items = count($post_valor);
        $sql = $sql ."," .pg_field_name($res, $j). " = ";
        foreach ($post_valor as $item){
          if($total_items == 1){
            $sql = $sql . "array['". $item . "'] ";
          }
          elseif($aux == 1){
            $sql = $sql . "array['". $item . "',";
          } elseif ($aux < $total_items){
            $sql = $sql . "'".$item . "', ";
          } else {
            $sql = $sql . "'".$item . "']";
          }
          $aux = $aux + 1;
        }
      } else {
        $sql = $sql . ", " . pg_field_name($res, $j). "='" .$post_valor . "'";
      }
    }
  }

  $sql = $sql . " where " .$campo_id. "='" .$id."'";
  AuditMgr::createAudit($_SESSION["nome"], "UPDATE", $sql);

  $res = pg_query($dbcon, $sql);

  if ($res) {
    print "<script>alert('Registro salvo com sucesso.'); window.close();</script>";
    pg_close($dbcon); 
  }
  else {
    echo "<h2>Erro na atualização dos dados</h2>";
    //Consulta sql com o registro a ser inserido na tabela
    print "SQL:<br>".$sql."<br>";
    $error = pg_last_error($dbcon);
    echo "<br><span style='color: red'>$error</span>";
    exit;
  }
?>

</body>
</html>
