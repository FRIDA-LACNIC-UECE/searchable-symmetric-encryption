<?php
       // acessar id e nome do usuario ativo atraves de $_SESSION['id_usuario']e $_SESSION['nome']
   require_once($_SERVER['DOCUMENT_ROOT']."/pages/Head.php");
   $gs = $_GET["gruposelecionado"];
   print "<font face='arial' size='2' color='silver'><b>Administrador>Consulta>Permissões do Grupo</b></font>";
   print "<br><br>";
   include ("../conexaoBD.php");
   

    print "<table width='70%' cellspacing='0' >";
    print " <form name='TextForm' method='get' target='_self'>";
    // Insere na página o combobox da seleção do grupo
    print "  <tr>";
    print "    <td width='70%' height='21' bgcolor='#4e78b1' align='right'>";
    print "        <b><font color='#800000'>Selecione um Grupo: </font></b>";
                   $sql = "select idgrupo, descricao from administrador.grupo order by descricao";
                   $registros = pg_query($dbcon, $sql); 
                   print "<select size='1' name='gruposelecionado'>";
                     while ($rowCombo = pg_fetch_row($registros)) {
                	    if ($gs==$rowCombo[0]) 
                           print "<option selected value='".$rowCombo[0]."' >".$rowCombo[1]."</option>";
                        else { print "<option value='".$rowCombo[0]."' >".$rowCombo[1]."</option>"; If ($gs==Null) $gs=$rowCombo[0]; }
                     }
    print "        </select></td>";
    print "    <td width='30%' height='21' bgcolor='#4e78b1' align='center'><input type='submit' value='Consultar' ></td>";
    print "  </tr>";
    print " </form>";
    print "</table>";
    
    // Permissoes de  menu
    print "<table width='70%' cellspacing='0' >";
    $sql = "select * from administrador.peritemsubmenu where idgrupo='".$gs."' ";
    $res = pg_query($dbcon, $sql);
    $record= pg_num_rows($res);
    print "  <tr>";
    print "    <td width='10%' height='21' bgcolor='#4e78b1'></td>";
    print "    <td width='90%' height='21' bgcolor='#4e78b1' align='center'><b>Itens Permitidos no Grupo: " .$record. ".</b></td>";
    print "  </tr>";
    print "</table>";

	print "<table cellspacing='0' id='sample' style='width:70%;'>";
	print "<thead>";
	print "	 <tr style='background: #eeeeee;'>";
	print "		<th style='width: 30%;'>Menu</th>";
	print "		<th style='width: 35%;'>Submenu</th>";
	print "		<th style='width: 35%;'>Item</th>";
    print "	 </tr>";
	print "</thead>";
	print "<tbody>";

    while ($row = pg_fetch_row($res)) {
      $sql = "select descricao, idsubmenu from menu_submenu where idmenu_submenu='".$row[1]."' ";
      $registros = pg_query($dbcon, $sql);
      while ($row3 = pg_fetch_row($registros)) {
         $item=$row3[0]; $idsubmenu=$row3[1];
      }
      $sql = "select descricao, idmenu from submenu where idsubmenu='".$idsubmenu."' ";
      $registros = pg_query($dbcon, $sql);
      while ($row3 = pg_fetch_row($registros)) {
         $submenu=$row3[0]; $idmenu=$row3[1];
      }
      $sql = "select descricao from menu where idmenu='".$idmenu."' ";
      $registros = pg_query($dbcon, $sql);
      while ($row3 = pg_fetch_row($registros)) {
         $menu=$row3[0];
      }
      print "<tr>";
	  print "	<td>".$menu."</td>";
   	  print "	<td>".$submenu."</td>";
	  print "	<td>".$item."</td>";
	  print "</tr>";
    }
	print "</tbody>";
	print "</table>";

   print "<script type='text/javascript'>Richtag.doTableLayout('sample')</script>";

   pg_close($dbcon);
   require_once($_SERVER['DOCUMENT_ROOT']."/pages/Foot.php");
   
?>
