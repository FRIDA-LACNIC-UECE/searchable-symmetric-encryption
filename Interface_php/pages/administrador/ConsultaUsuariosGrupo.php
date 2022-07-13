<?php
       // acessar id e nome do usuario ativo atraves de $_SESSION['id_usuario']e $_SESSION['nome']
   require_once($_SERVER['DOCUMENT_ROOT']."/pages/Head.php");

   print "<font face='arial' size='2' color='silver'><b>Administrador>Consulta>Usu&aacute;rios do Grupo</b></font>";
   print "<br><br>";
   include ("../conexaoBD.php");
   $gs = $_GET["gruposelecionado"];

    print "<table width='100%' cellspacing='0' >";
    print " <form name='TextForm' method='get' target='_self'>";
    // Insere na página o combobox da seleção do grupo
    print "  <tr>";
    print "    <td width='4%' height='21' bgcolor='#4e78b1'></td>";
    print "    <td width='16%' height='21' bgcolor='#4e78b1' align='left'>";
    print "    <td width='50%' height='21' bgcolor='#4e78b1' align='right'>";
    print "        <b><font color='#800000'>Selecione um Grupo: </font></b>";
                   $sql = "select idgrupo, descricao from administrador.grupo order by descricao";
                   $registros = pg_query($dbcon, $sql);
                   print "<select size='1' name='gruposelecionado'>";
                     while ($rowCombo = pg_fetch_row($registros)) {
                	    if ($gs==$rowCombo[0])
                           print "<option selected value='".$rowCombo[0]."' >".$rowCombo[1]."</option>";
                        else {
						   print "<option value='".$rowCombo[0]."' >".$rowCombo[1]."</option>";
						   If ($gs==Null) $gs=$rowCombo[0];
						}
                     }
    print "        </select></td>";
    print "    <td width='30%' height='21' bgcolor='#4e78b1'><input type='submit' value='Consultar' ></td>";
    print "  </tr>";
    $sql = "select * from administrador.usuario where idgrupo='".$gs."' order by nome";
    $res = pg_query($dbcon, $sql);
    $record= pg_num_rows($res);
    print "  <tr>";
    print "    <td width='4%' height='21' bgcolor='#4e78b1'></td>";
    print "    <td width='16%' height='21' bgcolor='#4e78b1' align='left'>";
    print "    <td width='50%' height='21' bgcolor='#4e78b1' align='right'><b>Usuários Cadastrados no Grupo: " .$record. ".</b></td>";
    print "    <td width='30%' height='21' bgcolor='#4e78b1'></td>";
    print "  </tr>";
    print " </form>";
    print "</table>";
    
	print "<table cellspacing='0' id='sample' style='width:100%;'>";
	print "<thead>";
	print "	 <tr style='background: #eeeeee;'>";
	print "		<th style='width: 7%;'>Id.</th>";
	print "		<th style='width: 33%;'>Nome</th>";
	print "		<th style='width: 15%;'>Conta</th>";
	print "		<th style='width: 15%;'>Senha</th>";
	print "		<th style='width: 30%;'>E-Mail</th>";
	print "	 </tr>";
	print "</thead>";
	print "<tbody>";
    while ($row = pg_fetch_row($res)) {
      print "<tr>";
	  print "	<td>".$row[0]."</td>";
	  print "	<td>".$row[1]."</td>";
	  print "	<td>".$row[2]."</td>";
	  print "	<td>".$row[3]."</td>";
	  print "	<td>".$row[4]."</td>";
      print "</tr>";
    }
	print "</tbody>";
	print "</table>";


   print "<script type='text/javascript'>Richtag.doTableLayout('sample')</script>";

   pg_close($dbcon);
   require_once($_SERVER['DOCUMENT_ROOT']."/pages/Foot.php");
   
?>
