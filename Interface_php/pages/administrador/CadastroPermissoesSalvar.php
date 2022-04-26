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
  include ("../conexaoBD.php");
  $idgrupo=$_POST["idgrupo"];
  $sql = "SELECT descricao from administrador.grupo WHERE idgrupo = ".$idgrupo;
  $res = pg_query($dbcon, $sql);
  $rotulo = pg_fetch_row($res)[0];
  print "<h2 style='padding-left:1%'>Cadastro e atualização das permissões do grupo: ".$rotulo."</h2>";

  $menuid = array(); $menuused = array(); 
  $submenuid = array(); $submenuused = array(); $submenuIdmenu = array();
  $itemid = array(); $itemIdsubmenu = array(); $itemused = array(); $id = array();

  // ler os registros da tabela menu
  $sql = "select idmenu from menu order by idmenu";
  $res = pg_query($dbcon, $sql); $m=0;
  while ($rowCombo = pg_fetch_row($res)) {
    $menuid[$m]=$rowCombo[0];
    $menuused[$m]=0;
    $m=$m + 1;
  }

  // ler os registros da tabela submenu
  $sql = "select idsubmenu, idmenu from submenu order by idsubmenu";
  $res = pg_query($dbcon, $sql); $n=0;
  while ($rowCombo = pg_fetch_row($res)) {
    $submenuid[$n]=$rowCombo[0];
    $submenuused[$n]=0;
    $submenuIdmenu[$n]=$rowCombo[1];
    $n=$n + 1;
  }

  // ler os registros da tabela menu_submenu 
  $sql = "select idmenu_submenu, idsubmenu from menu_submenu order by idsubmenu";
  $res = pg_query($dbcon, $sql); $i=0;
  while ($rowCombo = pg_fetch_row($res)) {
    $itemid[$i]=$rowCombo[0];
    $itemIdsubmenu[$i]=$rowCombo[1];
    $itemused[$i]=0;
    $i=$i + 1;
  }

  // ler as permissoes do grupo
  for ($j=0; $j<$i; $j++) {
    if (isset($_POST[$itemid[$j]])) {
      $id[$j]=$_POST[ $itemid[$j] ];
      if ((strcmp($id[$j],"on")==0)) {
        $itemused[$j]=1;
        $rm=0; $rn=0;
        while ($rn<$n) {
          if ($itemIdsubmenu[$j]==$submenuid[$rn]) {
            $submenuused[$rn]=1;
            while ($rm<$m) {
              if ($menuid[$rm]==$submenuIdmenu[$rn]) {
                $menuused[$rm]=1;
                $rm= $m;
              }
              $rm= $rm + 1;
            }
            $rn=$n;
          }
          $rn=$rn + 1;
        }
      }
    }
    else {
      continue;
    }
  }

  // insere as novas permissoes do grupo em peritemsubmenu
  $sql = "insert into administrador.peritemsubmenu (idperitemsubmenu, iditemsubmenu, idgrupo) values (";
  for ($j = 0; $j < $i; $j++) {
    $st= "select * from administrador.peritemsubmenu where (iditemsubmenu='".$itemid[$j]."' and idgrupo='".$idgrupo."')";
    $rt= pg_query($dbcon, $st);
    $record= pg_num_rows($rt);
    if ($itemused[$j]==1) {
      if ($record==0) { // insere o registro na tabela se ele ainda n�o existe nela
        $sql_max = "select max(idperitemsubmenu) from administrador.peritemsubmenu";
        $res_max = pg_query($dbcon, $sql_max);
        $idFetch = pg_fetch_row($res_max);
        $id_max = $idFetch[0] == '' ? '1' : $idFetch[0] + 1;
        $sqlvalor="'".$id_max."', '".$itemid[$j]."', '".$idgrupo."')";
        $sql2=$sql. $sqlvalor;

        $res = pg_query($dbcon, $sql2);
        if (!$res) {
          echo "<br> Erro na inserção dos dados devido inconsistência (tabela peritemsubmenu).";
          exit;
        }
      }
    }
    else { // item de submenu n�o vai ser cadastrado na tabela peritemsubmenu
      if ($record>0) { // deleta o registro da tabela
        $sql2="delete from administrador.peritemsubmenu where (iditemsubmenu='".$itemid[$j]."' and idgrupo='".$idgrupo."')";
        $res = pg_query($dbcon, $sql2);
        if (!$res) {
          echo "<br> Erro na dele&ccedil;&atilde;o dos dados devido inconsist&ecirc;ncia (tabela peritemsubmenu).";
          exit;
        }
      }
    }
  }

  // insere as novas permissoes do grupo em persubmenu
  $sql = "insert into administrador.persubmenu (idpersubmenu, idsubmenu, idgrupo) values (";
  for ($j = 0; $j < $n; $j++) {
    $st= "select * from administrador.persubmenu where (idsubmenu='".$submenuid[$j]."' and idgrupo='".$idgrupo."')";
    $rt= pg_query($dbcon, $st);
    $record= pg_num_rows($rt);
    if ($submenuused[$j]==1) {
      if ($record==0) { // insere o registro na tabela se ele ainda n�o existe nela
        $sql_max = "select max(idpersubmenu) from administrador.persubmenu";
        $res_max = pg_query($dbcon, $sql_max);
        $idFetch = pg_fetch_row($res_max);
        $id_max = $idFetch[0] == '' ? '1' : $idFetch[0] + 1;
        $sqlvalor="'".$id_max."', '".$submenuid[$j]."', '".$idgrupo."')";
        $sql2=$sql. $sqlvalor;
        $res = pg_query($dbcon, $sql2);
        if (!$res) {
          echo "<br> Erro na inserção dos dados devido inconsistência (tabela persubmenu).";
          exit;
        }
      }
    }
    else { // submenu n�o vai ser cadastrado na tabela persubmenu
      if ($record>0) { // deleta o registro da tabela
        $sql2="delete from administrador.persubmenu where (idsubmenu='".$submenuid[$j]."' and idgrupo='".$idgrupo."')";
        $res = pg_query($dbcon, $sql2);
        if (!$res) {
          echo "<br> Erro na deleção dos dados devido inconsistência (tabela persubmenu).";
          exit;
        }
      }
    }
  }
  // insere as novas permissoes do grupo em permenu
  $sql = "insert into administrador.permenu (idpermenu, idmenu, idgrupo) values (";
  for ($j = 0; $j < $m; $j++) {
    $st= "select * from administrador.permenu where (idmenu='".$menuid[$j]."' and idgrupo='".$idgrupo."')";
    $rt= pg_query($dbcon, $st);
    $record= pg_num_rows($rt);
    if ($menuused[$j]==1) {
      if ($record==0) { // insere o registro na tabela se ele ainda n�o existe nela
        $sql_max = "select max(idpermenu) from administrador.permenu";
        $res_max = pg_query($dbcon, $sql_max);
        $idFetch = pg_fetch_row($res_max);
        $id_max = $idFetch[0] == '' ? '1' : $idFetch[0] + 1;
        $sqlvalor="'".$id_max."', '".$menuid[$j]."', '".$idgrupo."')";
        $sql2=$sql. $sqlvalor;
        $res = pg_query($dbcon, $sql2);
        if (!$res) {
          echo "<br> Erro na inserção dos dados devido inconsistência (tabela permenu).";
          exit;
        }
      }
    }
    else { // item de menu n�o vai ser cadastrado na tabela permenu
      if ($record>0) { // deleta o registro da tabela
        $sql2="delete from administrador.permenu where (idmenu='".$menuid[$j]."' and idgrupo='".$idgrupo."')";
        $res = pg_query($dbcon, $sql2);
        if (!$res) {
          echo "<br> Erro na deleção dos dados devido inconsistência (tabela permenu).";
          exit;
        }
      }
    }
  }

  // Permissoes realizadas com sucesso
  print "<script>alert('Dados atualizados com sucesso.'); window.close();</script>";

  pg_close($dbcon); 
?>

</body>
</html>
