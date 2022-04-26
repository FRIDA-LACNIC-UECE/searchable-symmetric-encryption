<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN""http://www.w3.org/TR/html4/loose.dtd">
<html lang='pt-BR'>
<head>
<title>Digital Shield Security</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="shortcut icon" href="../styles/images/favicon.ico">
<link rel="stylesheet" type="text/css" href="/styles/global.css">

<style type="text/css">
  p {
    display: none;
  }
  p:target {
    display: block;
  }
</style>

</head>

<body>
<?php
  session_start(); // acessar id, nome e grupo do usuario ativo atraves de $_SESSION['id_usuario']['nome']['idgrupo'] 
  $id= $_GET["id"];
  include ("../conexaoBD.php");
  $sql = "SELECT descricao from administrador.grupo WHERE idgrupo = ".$id;
  $res = pg_query($dbcon, $sql);
  $rotulo = pg_fetch_row($res)[0];
  print "<h2 style='padding-left:1%'>Cadastro e atualização das permissões do grupo: ".$rotulo."</h2>";
?>

<form name="TextForm" action="CadastroPermissoesSalvar.php" method="post" target="_top">

  <input type="hidden" name="idgrupo" value="<?php print "$id"; ?>">
  <table width="100%" cellspacing="0" height="30">

<?php
  // Combobox com id e descricao dos grupos
  print "<tr>";
  print "  <td width='4%' height='21' bgcolor='#4e78b1'></td>";
  print "  <td width='58%' height='21' align='right' bgcolor='#4e78b1'><b>Grupo: $rotulo</b></td>";
  print "  <td width='38%' height='21' align='right' bgcolor='#4e78b1'>";
  print "    <input type='SUBMIT' value='Salvar'>&nbsp;&nbsp;&nbsp;&nbsp;";
  print "  </td>";
  print "</tr>";
  print "</table>";

  $menuid = array(); $menudesc = array(); 
  $submenuid = array(); $submenudesc = array(); $submenuIdmenu = array();
  $itemid = array(); $itemdesc = array(); $itemIdsubmenu = array(); $itemused = array();

  // ler os registros da tabela menu
  $sql = "select idmenu, descricao from menu order by ordem";
  $res = pg_query($dbcon, $sql); $m=0;
  while ($rowCombo = pg_fetch_row($res)) {
    $menuid[$m]=$rowCombo[0];
    $menudesc[$m]=$rowCombo[1];
    $m=$m + 1;
  }

  // ler os registros da tabela submenu
  $sql = "select idsubmenu, descricao, idmenu from submenu order by idmenu, ordem";
  $res = pg_query($dbcon, $sql); $n=0; $i=0;
  while ($rowCombo = pg_fetch_row($res)) {
    $submenuid[$n]=$rowCombo[0];
    $submenudesc[$n]=$rowCombo[1];
    $submenuIdmenu[$n]=$rowCombo[2];
    $n=$n + 1;
    // ler os registros da tabela menu_submenu
    $sql2 = "select idmenu_submenu, descricao, idsubmenu from menu_submenu where idsubmenu='".$rowCombo[0]."' order by ordem";
    $res2 = pg_query($dbcon, $sql2); 
    while ($row2 = pg_fetch_row($res2)) {
      $itemid[$i]=$row2[0];
      $itemdesc[$i]=$row2[1];
      $itemIdsubmenu[$i]=$row2[2];
      $i=$i+1;
    }
  }

  // ler os itens de submenus permitidos do grupo
  for ($j=0; $j<$i; $j++) $itemused[$j]=0;
  $sql = "select iditemsubmenu from administrador.peritemsubmenu where (idgrupo='".$id."') order by iditemsubmenu";
  $res = pg_query($dbcon, $sql); 
  while ($rowCombo = pg_fetch_row($res)) {
    $ri=0;
    while ($ri<$i) {
      if ($rowCombo[0]==$itemid[$ri]) {
        $itemused[$ri]=1;
        $ri= $i;
      }
      $ri= $ri + 1;
    }
  }

  print "<table border='0' cellspacing='0' id='sample' style='width:100%;'>";
  print "<thead>";
  print "  <tr style='background: #eeeeee;'>";
  print "    <th align='left' style='width: 25%;'>Menu</th>";
  print "    <th align='left' style='width: 75%;'>Submenu/Permissão de Item de Submenu</th>";
  print "  </tr>";
  print "</thead>";
  print "<tbody>"; $rm=0;

  while ($rm<$m) {
    $menu=$menudesc[$rm]; $rn=0; $z=0;
    while ($rn<$n) {
      if ($menuid[$rm]==$submenuIdmenu[$rn]) {
        $submenu=$submenudesc[$rn];
        if ($z==0) {
          print "<tr>";
          print "	<td align='left'>".$menu."</td>";
          print "	<td align='left'><a href='#submenu".$submenuid[$rn]."'>".$submenu."</a></td>"; 
          print "</tr>";
          $z=1;
        }
        else {
          print "<tr>";
          print "	<td align='left'> </td>";
          print "	<td align='left'><a href='#submenu".$submenuid[$rn]."'>".$submenu."</a></td>"; 
          print "</tr>";
        }
        print "<tr>";   
        print "	<td align='left'> </td>"; 
        print "	<td align='left'><p id='submenu".$submenuid[$rn]."'>"; $k=0;  
        while ($k<$i) {
          if ($itemIdsubmenu[$k]==$submenuid[$rn]) {
            if ($itemused[$k]==1) print "<input  type='checkbox' name='" .$itemid[$k]. "' checked>";
            else print "<input  type='checkbox' name='" .$itemid[$k]. "'>";
            print " ".$itemdesc[$k]."<br>";
          }
          $k=$k+1;
        }
        print "</p></td></tr>"; 
      }
    $rn= $rn + 1;
    }
    $rm=$rm+1;
  }
  print "</tbody>";
  print "</table>";

  print "</form>";

  pg_close($dbcon); 

?>

</body>
</html>
